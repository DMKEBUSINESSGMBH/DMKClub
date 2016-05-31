<?php

namespace DMKClub\Bundle\BasicsBundle\Job\Pdf;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\ImportExportBundle\Processor\ContextAwareProcessor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

/**
 * Die Klasse schreibt das PDF in das Ziel-Filesystem
 * @author "René Nitzsche"
 */
class PdfWriter implements ItemWriterInterface {

	/**
	 * @var Filesystem|null
	 */
	protected $fileSystem = NULL;
	/**
	 */
	public function __construct(FilesystemMap $fileSystemMap, $fs) {
		try {
			$this->fileSystem = $fileSystemMap->get($fs);
		}
		catch(\Exception $e) {
			// Filesystem not configured...
		}
	}

	/* (non-PHPdoc)
	 * @see \Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface::write()
	 */
	public function write(array $items) {
		print_r(['write' => 1, 'items' => $items]);


	}

}
