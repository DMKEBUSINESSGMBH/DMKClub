<?php

namespace DMKClub\Bundle\BasicsBundle\Job\Pdf;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\ImportExportBundle\Processor\ContextAwareProcessor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;

/**
 * Die Klasse erzeugt das notwendige PDF
 * @author "René Nitzsche"
 */
class PdfProcessor implements ContextAwareProcessor {
	/**
	 * @var ContextRegistry
	 */
	protected $contextRegistry;

	/**
	 * @var Manager
	 */
	protected $pdfManager;

	/**
	 * TODO: wird die registry benötigt?
	 * @param Manager $pdfManager
	 */
	public function __construct(Manager $pdfManager) {
		$this->pdfManager = $pdfManager;
	}

	/**
	 * Processes entity to generate pdf
	 *
	 * @param mixed $object
	 * @return array
	 * @throws RuntimeException
	 */
	public function process($object) {
		print_r(['object'=>get_class($object), 'process'=>1]);

		try {
			$fileName = $this->pdfManager->buildPdf($object);
		}
		catch(Exception $e) {
			return null;
		}

		return $fileName;
	}

	/**
	 * @param ContextInterface $context
	 * @throws InvalidConfigurationException
	 */
	public function setImportExportContext(ContextInterface $context)
	{
		$this->context = $context;
	}
}
