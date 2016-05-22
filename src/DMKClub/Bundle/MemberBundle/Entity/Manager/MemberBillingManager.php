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
use Symfony\Component\Validator\Constraints\Null;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;

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

	public function __construct(EntityManager $em, ContainerInterface $container, ProcessorProvider $processorProvider,
			DynamicSegmentQueryBuilder $dynamicSegmentQueryBuilder, StaticSegmentQueryBuilder $staticSegmentQueryBuilder) {
		$this->em = $em;
		$this->setContainer($container);
		$this->processionProvider = $processorProvider;
		$this->dynamicSegmentQueryBuilder = $dynamicSegmentQueryBuilder;
		$this->staticSegmentQueryBuilder = $staticSegmentQueryBuilder;
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
			if(($hits + $skipped) > 10)
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
		$position->setFlag('FEECORRECTION');
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
	public function startAccounting(MemberBilling $memberBilling) {
		$callback = function (Member $member, MemberBilling $billing, $processor) {
			if($this->hasFee4Billing($member, $billing)) {
				return null;
			}
			$memberFee = $processor->execute($member);
			return $memberFee;
		};
		return $this->doAccounting($memberBilling, $callback);
	}
	/**
	 * Starts account process for given billing.
	 *
	 * @param MemberBilling $entity
	 * @param \Closure $callback
	 * @param boolean $correction
	 * @return array
	 */
	protected function doAccounting(MemberBilling $memberBilling, $callback) {
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

		$q = $qb->getQuery();

		$result = $q->iterate();
		$hits = 0;
		$skipped = 0;
		$errors = [];

		foreach ($result As $row) {
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
			if($hits > 10)
				break;
		}
		$this->em->flush();
		// jetzt die Summe holen und im Billing speichern
		$this->updateSummary($memberBilling);

		return ['success' => ($hits), 'skipped' => $skipped, 'errors'=>$errors];
	}
	protected function updateSummary(MemberBilling $memberBilling) {
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
