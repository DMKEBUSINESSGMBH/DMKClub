<?php

namespace DMKClub\Bundle\MemberBundle\Job\Accounting;

use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use Oro\Bundle\ImportExportBundle\Processor\ContextAwareProcessor;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Psr\Log\LoggerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use Doctrine\ORM\EntityManager;

/**
 * Die Klasse erzeugt die MemberFee
 * @author "René Nitzsche"
 */
class FeeProcessor implements ItemProcessorInterface {

	/** @var \Psr\Log\LoggerInterface */
	private $logger;
	private $billingManager;

	/**
	 * TODO:
	 * @param Manager $pdfManager
	 */
	public function __construct(MemberBillingManager $billingManager, LoggerInterface $logger) {
		$this->billingManager = $billingManager;
		$this->logger = $logger;
	}

	/**
	 * Processes entity to generate pdf
	 *
	 * @param MemberFee $item
	 * @return MemberFee
	 * @throws RuntimeException
	 */
	public function process($item) {

		try {
			$memberFee = $this->billingManager->calculateMemberFee($item->getBilling(), $item->getMember());
			if($memberFee->getPriceTotal() == 0) {
				// Ohne Beitrag muss kein Datensatz angelegt werden
				return null;
			}
			$memberFee->setBillDate($item->getBillDate());
			$memberFee->setOrganization($memberFee->getBilling()->getOrganization());
			$memberFee->setOwner($memberFee->getBilling()->getOwner());
		}
		catch(\Exception $e) {
			// Abbruch bei Fehler
			$this->logger->error('MemberFee calculation fails', [
					'member id' => $item->getMember()->getId(),
					'billing id' => $item->getBilling()->getId(),
					'error' => $e->getMessage(),
			]);
			return null;
		}

		return $memberFee;
	}
}
