<?php

namespace DMKClub\Bundle\BasicsBundle\Job\Pdf;

use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Exception\InvalidConfigurationException;
use Oro\Bundle\ImportExportBundle\Reader\AbstractReader;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;

/**
 * Die Klasse liest aus der Config die Entities, für die ein PDF erzeugt werden soll
 * @author "René Nitzsche"
 *
 */
class ItemReader extends AbstractReader {
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
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param ContextRegistry $contextRegistry
	 */
	public function __construct(ContextRegistry $contextRegistry, EntityManager $em) {
		parent::__construct($contextRegistry);
		$this->em = $em;
	}

	/**
	 * {@inheritdoc}
	 */
	public function read() {
		print_r(['read'=>1, 'readCount'=>$this->getContext()->getReadCount(), 'ids' => $this->entityIds]);

		if (count($this->entityIds) > 0) {
			$itemIdx = $this->getContext()->getReadCount();
			$nextItem = array_key_exists($itemIdx, $this->entityIds) ? $this->entityIds[$itemIdx] : NULL;
			if($nextItem !== null) {
				$nextItem = $this->resolveEntity($nextItem, $this->entityName);
				if($nextItem !== null) {
					$this->getContext()->incrementReadCount();
				}
			}
			return $nextItem;
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
	 * {@inheritdoc}
	 */
	protected function initializeFromContext(ContextInterface $context) {
		// Hier prüfen, ob es noch
		if (! $context->hasOption('entity_name')) {
			throw new InvalidConfigurationException('Configuration reader must contain "entity_name".');
		} else {
			$this->entityName = $context->getOption('entity_name');
		}
		if (! $context->hasOption('entity_ids')) {
			throw new InvalidConfigurationException('Configuration reader must contain "entity_ids".');
		} else {
			$this->entityIds = explode(',', $context->getOption('entity_ids'));
		}
	}
}
