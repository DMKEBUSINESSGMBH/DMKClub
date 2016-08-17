<?php

namespace DMKClub\Bundle\MemberBundle\Job\Accounting;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\ImportExportBundle\Processor\ContextAwareProcessor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Akeneo\Bundle\BatchBundle\Entity\StepExecution;
use DMKClub\Bundle\BasicsBundle\PDF\PdfException;
use Gaufrette\Filesystem;
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
			$this->em->persist($item);
			$billings[$item->getBilling()->getId()] = $item->getBilling();
			$this->stepExecution->incrementWriteCount();
		}
		$this->em->flush();
		// Jetzt noch die Summe aktualisieren
		foreach($billings As $billing)
			$this->billingManager->updateSummary($billing);

	}
	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface::setStepExecution()
	 */
	public function setStepExecution(StepExecution $stepExecution) {
		$this->stepExecution = $stepExecution;
	}
}
