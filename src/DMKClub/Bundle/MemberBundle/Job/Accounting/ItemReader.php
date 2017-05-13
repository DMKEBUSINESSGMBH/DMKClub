<?php

namespace DMKClub\Bundle\MemberBundle\Job\Accounting;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\ImportExportBundle\Reader\AbstractReader;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

/**
 * Die Klasse liest aus der Config die Members, für die ein Beitrag erzeugt werden soll.
 * Der Reader liefert dann eine Dummy-MemberFee mit Zugriff auf den Member und die Billing
 * @author "René Nitzsche"
 */
class ItemReader extends AbstractReader {
	const OPTION_MEMBERBILLING = 'memberbilling_id';
	const OPTION_ENTITIES = 'entity_ids';
	const OPTION_FEEDATE = 'fee_date';

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * @var String
	 */
	protected $entityName;

	/**
	 * @var array
	 */
	protected $entityIds;

	/**
	 * @var \DateTime
	 */
	protected $feeDate;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/** @var MemberBillingManager */
	private $billingManager;
	private $memberBilling;
	/**
	 * @param ContextRegistry $contextRegistry
	 */
	public function __construct(ContextRegistry $contextRegistry, EntityManager $em, MemberBillingManager $billingManager) {
		parent::__construct($contextRegistry);
		$this->em = $em;
		$this->billingManager = $billingManager;
	}

	/*
	 * Entity holen und Ziel für Export ermitteln. Letzteres wird in den Context gelegt.
	 * {@inheritdoc}
	 */
	public function read() {
		if (count($this->entityIds) > 0) {
			$itemIdx = $this->getContext()->getReadCount();
			$nextItem = array_key_exists($itemIdx, $this->entityIds) ? $this->entityIds[$itemIdx] : NULL;
			while($nextItem !== null) {
				$this->getContext()->incrementReadCount();
				// Member laden
				$member = $this->billingManager->getMemberRepository()->findOneById($nextItem);
				// Gibt es für den Member schon eine Fee?
				if($this->billingManager->hasFee4Billing($member, $this->memberBilling)) {
					$itemIdx = $this->getContext()->getReadCount();
					// Diese id muss ausgelassen werden.
					$nextItem = array_key_exists($itemIdx, $this->entityIds) ? $this->entityIds[$itemIdx] : NULL;
				}
				else {
					$memberFee = new MemberFee();
					$memberFee->setMember($member);
					$memberFee->setBilling($this->memberBilling);
					$memberFee->setFeeDate($this->feeDate);
					return $memberFee;
				}
			}
		}
		return null;
	}
	/**
	 *
	 * @param int $itemId
	 * @param string $entityName
	 * @return object|null
	 */
	protected function resolveEntity($itemId, $entityName) {
		$repo = $this->em->getRepository($entityName);
		return $repo->findOneById($itemId);
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberBillingRepository
	 */
	public function getMemberBillingRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:MemberBilling');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function initializeFromContext(ContextInterface $context) {
		if (! $context->hasOption(self::OPTION_MEMBERBILLING)) {
		    throw new InvalidConfigurationException('Configuration reader must contain "'.self::OPTION_MEMBERBILLING.'".');
		} else {
		    $bid = (int) $context->getOption(self::OPTION_MEMBERBILLING);
			$this->memberBilling = $this->getMemberBillingRepository()->findOneById($bid);
			if(!$this->memberBilling)
				throw new InvalidConfigurationException('Cannot resolve member billing with id ['.$bid.'] .');
		}
		if (! $context->hasOption(self::OPTION_ENTITIES)) {
		    throw new InvalidConfigurationException('Configuration reader must contain "'.self::OPTION_ENTITIES.'".');
		} else {
		    $this->entityIds = explode(',', $context->getOption(self::OPTION_ENTITIES));
		}
		if ( $context->hasOption(self::OPTION_FEEDATE)) {
		    $this->feeDate = new \DateTime($context->getOption(self::OPTION_FEEDATE));
		}
		else {
		    $this->feeDate = new \DateTime();
		}


	}


}
