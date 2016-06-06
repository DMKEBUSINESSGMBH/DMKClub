<?php

namespace DMKClub\Bundle\BasicsBundle\Job\Pdf;

use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use Oro\Bundle\ImportExportBundle\Processor\ContextAwareProcessor;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Die Klasse erzeugt das notwendige PDF
 * @author "René Nitzsche"
 */
class PdfProcessor implements ItemProcessorInterface {

	/**
	 * @var Manager
	 */
	protected $pdfManager;
	/** @var \Psr\Log\LoggerInterface */
	private $logger;

	/**
	 * TODO: wird die registry benötigt?
	 * @param Manager $pdfManager
	 */
	public function __construct(Manager $pdfManager, LoggerInterface $logger) {
		$this->pdfManager = $pdfManager;
		$this->logger = $logger;
	}

	/**
	 * Processes entity to generate pdf
	 *
	 * @param mixed $object
	 * @return string path to pdf file
	 * @throws RuntimeException
	 */
	public function process($object) {
		try {
			$fileName = $this->pdfManager->buildPdf($object);
		}
		catch(Exception $e) {
			// Abbruch bei Fehler
			$this->logger->error('pdf creation failed', [
					'entity class' => get_class($object),
					'id' => $object->getId(),
					'error' => $e->getMessage(),
			]);
			return null;
		}

		return $fileName;
	}
}
