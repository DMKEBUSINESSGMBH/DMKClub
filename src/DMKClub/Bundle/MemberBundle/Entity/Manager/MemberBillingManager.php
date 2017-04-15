<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Model\Processor;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use DMKClub\Bundle\MemberBundle\Accounting\AccountingException;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\SegmentBundle\Query\StaticSegmentQueryBuilder;
use Oro\Bundle\SegmentBundle\Query\DynamicSegmentQueryBuilder;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use DMKClub\Bundle\BasicsBundle\Job\JobExecutor;

class MemberBillingManager implements ContainerAwareInterface {
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var ContainerInterface
	 */
	protected $container;
	protected $processionProvider;
	/** @var DynamicSegmentQueryBuilder */
	protected $dynamicSegmentQueryBuilder;
	/** @var StaticSegmentQueryBuilder */
	protected $staticSegmentQueryBuilder;
	/** @var \DMKClub\Bundle\BasicsBundle\Job\JobExecutor */
	private $jobExecutor;

	public function __construct(EntityManager $em, ContainerInterface $container, ProcessorProvider $processorProvider,
			DynamicSegmentQueryBuilder $dynamicSegmentQueryBuilder, StaticSegmentQueryBuilder $staticSegmentQueryBuilder,
			JobExecutor $jobExecutor) {
		$this->em = $em;
		$this->setContainer($container);
		$this->processionProvider = $processorProvider;
		$this->dynamicSegmentQueryBuilder = $dynamicSegmentQueryBuilder;
		$this->staticSegmentQueryBuilder = $staticSegmentQueryBuilder;
		$this->jobExecutor = $jobExecutor;
	}

	/**
	 *
	 * @param MemberBilling $memberBilling
	 * @return \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface
	 */
	public function getProcessor(MemberBilling $memberBilling) {
		/* @var $provider \DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider */
		$provider = $this->container->get('dmkclub_member.memberbilling.processorprovider');
		$processor = $provider->getProcessorByName($memberBilling->getProcessor());
		return $processor;
	}
	/**
	 * Starts correction process for given billing.
	 * Das muss später bestimmt mal asynchon gemacht werden. Jetzt aber zunächst die direkte Umsetzung.
	 *
	 * @param MemberBilling $entity
	 * @return array
	 */
	public function startCorrections(MemberBilling $memberBilling) {
		$processor = $this->getProcessor($memberBilling);
		$processor->init($memberBilling, $this->getProcessorSettings($memberBilling));
		// Über alle Fees iterieren, die zur Abrechnung markiert sind
		$alias = 'f';
		$qb = $this->getMemberFeeRepository()->createQueryBuilder($alias);
		$qb->where($alias.'.billing = :bid AND '.$alias.'.correctionStatus = :status');
		$qb->setParameter('bid', $memberBilling->getId());
		$qb->setParameter('status', MemberFee::CORRECTION_STATUS_OPEN);
		$q = $qb->getQuery();

		$result = $q->iterate();
		$hits = 0;
		$skipped = 0;
		$limit = 50;
		$errors = [];

		foreach ($result As $row) {
			/* @var $existingFee \DMKClub\Bundle\MemberBundle\Entity\MemberFee */
			$existingFee = $row[0];
			$member = $existingFee->getMember();
			$memberFee = $processor->execute($member);
			// Jetzt prüfen, ob es eine Differenz der Beträge gibt und für diese Differenz eine
			// Position anlegen
			$diff = $memberFee->getPriceTotal() - $existingFee->getPriceTotal();
			if($diff) {
				$this->addCorrectionPosition($existingFee, $diff);
				$hits++;
			}
			else {
				$skipped += 1;
			}
			$existingFee->setCorrectionStatus(MemberFee::CORRECTION_STATUS_NONE);
			$this->em->persist($existingFee);
			if(($hits + $skipped) > $limit)
				break;
		}
		$this->em->flush();

		return ['success' => ($hits), 'skipped' => $skipped, 'errors'=>$errors];
	}

	protected function addCorrectionPosition(MemberFee $fee, $diff) {
		$position = new MemberFeePosition();
		$position->setQuantity(1);
		$position->setPriceSingle($diff);
		$position->setPriceTotal($diff);
		$labels = $fee->getBilling()->getPositionLabelMap();
		$correction = isset($labels[MemberFeePosition::FLAG_CORRECTION]) ?
			$labels[MemberFeePosition::FLAG_CORRECTION] : 'Correction [DATE]';
		$correction = str_replace('[DATE]', $fee->getUpdatedAt()->format('d.m.Y') ,$correction);
		$position->setDescription($correction);
		$position->setFlag(MemberFeePosition::FLAG_CORRECTION);
		$fee->addPosition($position);
		$fee->setPriceTotal($fee->getPriceTotal() + $diff);
	}
	/**
	 * Starts account process for given billing.
	 * Das muss später bestimmt mal asynchon gemacht werden. Jetzt aber zunächst die direkte Umsetzung.
	 *
	 * @param MemberBilling $entity
	 * @return array
	 */
	public function startAccounting(MemberBilling $memberBilling, $async = TRUE) {
		$callback = function (Member $member, MemberBilling $billing, $processor) {
			if($this->hasFee4Billing($member, $billing)) {
				return null;
			}
			$memberFee = $processor->execute($member);
			return $memberFee;
		};
		return $this->doAccounting($memberBilling, $callback, $async);
	}
	/**
	 * Calculate MemberFee for a single Member. The MemberFee is not persisted to database!
	 * And there is no check for still existing memberfees.
	 *
	 * @param MemberBilling $memberBilling
	 * @param Member $member
	 * @return MemberFee
	 */
	public function calculateMemberFee(MemberBilling $memberBilling, Member $member) {
		$processor = $this->getProcessor($memberBilling);
		$processor->init($memberBilling, $this->getProcessorSettings($memberBilling));
		$memberFee = $processor->execute($member);
		$memberFee->setMember($member);
		$memberFee->setBilling($memberBilling);
		return $memberFee;
	}
	/**
	 * Starts account process for given billing.
	 *
	 * @param MemberBilling $entity
	 * @param \Closure $callback
	 * @param boolean $correction
	 * @return array
	 */
	protected function doAccounting(MemberBilling $memberBilling, $callback, $async = TRUE) {
		$processor = $this->getProcessor($memberBilling);
		$processor->init($memberBilling, $this->getProcessorSettings($memberBilling));

		$segmentQuery = NULL;
		$segment = $memberBilling->getSegment();
		if($segment) {
			// Hier relevante Filter auf die Mitglieder setzen
			if ($segment->getType()->getName() === SegmentType::TYPE_DYNAMIC) {
				$segmentQuery = $this->dynamicSegmentQueryBuilder->build($segment);
			} else {
				$segmentQuery = $this->staticSegmentQueryBuilder->build($segment);
			}
		}

		$alias = 'm';
		$qb = $this->getMemberRepository()->createQueryBuilder($alias);
		if($segmentQuery != NULL) {
			$identifier = $alias.'.id';
			$segmentExpr = $qb->expr()->in($identifier, $segmentQuery->getDQL());
			$qb->where($segmentExpr);
			$params = $segmentQuery->getParameters();
			/** @var Parameter $param */
			foreach ($params as $param) {
				$qb->setParameter($param->getName(), $param->getValue(), $param->getType());
			}
		}

		if($async) // Bei async nur die IDs sammeln
			$qb->select($alias.'.id');
		$q = $qb->getQuery();

		$result = $q->iterate();
		$hits = 0;
		$limit = 50;
		$skipped = 0;
		$errors = [];

		$jobData = [
				'ids' => [],
		];
		$ids = [];

		foreach ($result As $row) {
			if($async) {
				$row = reset($row);
				$ids[] = $row['id'];
				$hits++;
			}
			else {
				/* @var $member \DMKClub\Bundle\MemberBundle\Entity\Member */
				$member = $row[0];
				try {
					$memberFee = $callback($member, $memberBilling, $processor);
					if($memberFee === null) {
						$skipped++;
						continue;
					}
					$memberFee->setBilling($memberBilling);
					$memberFee->setMember($member);
					$this->em->persist($memberFee);
				}
				catch(AccountingException $exception) {
					$errors[] = 'Member '. $member->getId() . ' - ' . $exception->getMessage();
				}
				$hits++;
				if($hits > $limit)
					break;
			}
		}
		if(!$async) {
			$this->em->flush();
			// jetzt die Summe holen und im Billing speichern
			$this->updateSummary($memberBilling);
		}
		else {
			$jobData['memberbilling_id'] = $memberBilling->getId();
			$jobData['entity_ids'] = implode(',', $ids);
			$jobType = 'export';
			$jobName = 'dmkfeeaccounting';

			$this->jobExecutor->createJob($jobType, $jobName, $jobData, true);
		}

		return ['success' => ($hits), 'skipped' => $skipped, 'errors'=>$errors, 'async' => $async];
	}
	/**
	 * Update totals for this MemberBilling
	 * @param MemberBilling $memberBilling
	 */
	public function updateSummary(MemberBilling $memberBilling) {
		$sub = 'SELECT sum(f.priceTotal) FROM DMKClubMemberBundle:MemberFee f WHERE f.billing = :bid';

		$q = $this->em->createQuery('UPDATE DMKClubMemberBundle:MemberBilling b
				SET b.feeTotal = ('.$sub.')
				WHERE b.id = :bid');
		$q->setParameter('bid', $memberBilling->getId());
		return $q->execute();
	}

	/**
	 * Wether or not a fee still exists
	 * @param Member $member
	 * @param MemberBilling $memberBilling
	 * @return boolean
	 */
	public function hasFee4Billing(Member $member, MemberBilling $memberBilling) {
		$fee = $this->getFee4Billing($member, $memberBilling);
		return $fee !== NULL;
	}
	/**
	 * returns a fee for this billing
	 * @param Member $member
	 * @param MemberBilling $memberBilling
	 * @return boolean
	 */
	public function getFee4Billing(Member $member, MemberBilling $memberBilling) {
		$fee = $this->getMemberFeeRepository()->findOneBy(['billing' => $memberBilling->getId(), 'member' => $member->getId()]);
		return $fee;
	}
	/**
	 * returns total payed fee amount
	 * @param MemberBilling $memberBilling
	 * @return int
	 */
	public function getPayedTotal(MemberBilling $memberBilling) {
		$dql = 'SELECT sum(f.payedTotal) payed FROM DMKClubMemberBundle:MemberFee f WHERE f.billing = :bid';

		$q = $this->em->createQuery($dql);
		$q->setParameter('bid', $memberBilling->getId());
		$result = $q->getArrayResult();
		$result = reset($result);
		return $result['payed'];
	}

	/**
	 * Return the configured options for selected processor
	 * @param MemberBilling $entity
	 * @return array
	 */
	public function getProcessorSettings(MemberBilling $entity) {
		// Hier müssen wir eingreifen. Die Storedaten sind serialisiert in der
		// processorConfig drin. Sie müssen in ein VO überführt und dann in
		// processorSetting gesetzt werden.
		// Beim Wechsel des processortypes muss man aber aufpassen, damit die Config noch passt!
		$data = $entity->getProcessorConfig();

		$data = $data ? unserialize($data) : [];
		$processorName = $entity->getProcessor();
		$result = [];
		if($processorName) {
			$storedData = isset($data[$processorName]) ? $data[$processorName] : [];
			$processor = $this->getProcessor($entity);
			// Bereinigung von veralteten Attributen
			foreach ($processor->getFields() As $fieldName) {
				if(array_key_exists($fieldName, $storedData))
					$result[$fieldName] = $storedData[$fieldName];
			}
		}
		return $result;
	}

	/**
	 * Liefert die registrierten Prozessoren
	 * @return [Processor]
	 */
	public function getProcessors() {
		$this->processionProvider->getProcessors();
	}
	/**
	 * Sets the Container.
	 *
	 * @param ContainerInterface|null $container A ContainerInterface instance or null
	 *
	 * @api
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
	 */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}
	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
	 */
	public function getMemberFeeRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:MemberFee');
	}
}
