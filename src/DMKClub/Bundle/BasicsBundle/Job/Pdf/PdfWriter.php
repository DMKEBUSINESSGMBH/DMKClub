<?php

namespace DMKClub\Bundle\BasicsBundle\Job\Pdf;

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

/**
 * Die Klasse schreibt das PDF in das Ziel-Filesystem
 * @author "René Nitzsche"
 */
class PdfWriter implements ItemWriterInterface, StepExecutionAwareInterface {
	/**
	 * @var StepExecution
	 */
	protected $stepExecution;

	/**
	 * @var FilesystemMap
	 */
	protected $fileSystemMap = NULL;
	/**
	 * @var Filesystem
	 */
	protected $fs = NULL;
	/**
	 */
	public function __construct(FilesystemMap $fileSystemMap) {
		$this->fileSystemMap = $fileSystemMap;
	}

	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface::write()
	 */
	public function write(array $items) {
		try {
			$fsName = $this->stepExecution->getExecutionContext()->get('target_fs');
			$this->fs = $this->fileSystemMap->get($fsName);
		}
		catch(\Exception $e) {
			// Filesystem not configured...
			throw new PdfException('Target filesystem not configured');
		}

		foreach ($items As $path) {
			$fileName = basename($path);
			$this->fs->write($fileName, file_get_contents($path));
			unlink($path); // Quelldatei löschen
			$this->stepExecution->incrementWriteCount();
		}
	}
	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface::setStepExecution()
	 */
	public function setStepExecution(StepExecution $stepExecution) {
		$this->stepExecution = $stepExecution;
	}

}
