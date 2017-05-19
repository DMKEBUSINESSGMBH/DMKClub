<?php

namespace DMKClub\Bundle\MemberBundle\Job\Accounting;

use Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use Doctrine\ORM\EntityManager;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;

/**
 * Die Klasse schreibt die Fee in die Datenbank
 * @author "René Nitzsche"
 */
class FeeWriter implements ItemWriterInterface, StepExecutionAwareInterface {
	/**
	 * @var StepExecution
	 */
	protected $stepExecution;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	/** @var MemberBillingManager */
	private $billingManager;

	/**
	 */
	public function __construct(MemberBillingManager $billingManager, EntityManager $em) {
		$this->em = $em;
		$this->billingManager = $billingManager;
	}

	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface::write()
	 */
	public function write(array $items) {
		$billings = [];
		foreach ($items As $item) {
			/* @var $item \DMKClub\Bundle\MemberBundle\Entity\MemberFee */
			$this->em->persist($item);

			$billings[$item->getBilling()->getId()] = $item->getBilling();
			$this->stepExecution->incrementWriteCount();
		}
		$this->em->flush();
		// Jetzt noch die Summe aktualisieren
		foreach($billings As $billing) {
		    $this->billingManager->updateSummary($billing);
		}

	}
	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface::setStepExecution()
	 */
	public function setStepExecution(StepExecution $stepExecution) {
		$this->stepExecution = $stepExecution;
	}
}
