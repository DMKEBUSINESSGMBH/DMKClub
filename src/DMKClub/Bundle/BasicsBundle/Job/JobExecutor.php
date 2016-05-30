<?php
namespace DMKClub\Bundle\BasicsBundle\Job;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Oro\Bundle\ImportExportBundle\Job\JobExecutor as OroJobExecutor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;

use Akeneo\Bundle\BatchBundle\Connector\ConnectorRegistry;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository as BatchJobRepository;

/**
 * Die Originalklasse wird überschrieben, um die Erstellung eine JobInstance von deren Ausführung zu trennen.
 *
 */
class JobExecutor extends OroJobExecutor {

	/**
	 * @param ConnectorRegistry $jobRegistry
	 * @param BatchJobRepository $batchJobRepository
	 * @param ContextRegistry $contextRegistry
	 * @param ManagerRegistry $managerRegistry
	 */
	public function __construct(
			ConnectorRegistry $jobRegistry,
			BatchJobRepository $batchJobRepository,
			ContextRegistry $contextRegistry,
			ManagerRegistry $managerRegistry
	) {
		parent::__construct($jobRegistry, $batchJobRepository, $contextRegistry, $managerRegistry);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Oro\Bundle\ImportExportBundle\Job\JobExecutor::createJobInstance()
	 */
	public function createJobInstance($jobType, $jobName, array $configuration) {
		parent::createJobInstance($jobType, $jobName, $configuration);
	}

}