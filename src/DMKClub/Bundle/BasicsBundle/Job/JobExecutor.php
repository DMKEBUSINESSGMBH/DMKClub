<?php
namespace DMKClub\Bundle\BasicsBundle\Job;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Oro\Bundle\ImportExportBundle\Job\JobExecutor as OroJobExecutor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;

use Akeneo\Bundle\BatchBundle\Connector\ConnectorRegistry;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository as BatchJobRepository;
use Akeneo\Bundle\BatchBundle\Entity\JobExecution;
use Akeneo\Bundle\BatchBundle\Entity\JobInstance;

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
	 * Create a new JobInstance and JobExecution without starting.
	 * @param string $jobType
	 * @param string $jobName
	 * @param array $configuration
	 * @return JobExecution
	 */
	public function createJob($jobType, $jobName, array $configuration) {
		$this->initialize();
		$jobInstance = $this->createJobInstance($jobType, $jobName, $configuration);
		$jobExecution = $this->createJobExecution($configuration, $jobInstance);
		return $jobExecution;
	}
	/**
	 * (non-PHPdoc)
	 * @see \Oro\Bundle\ImportExportBundle\Job\JobExecutor::doJob()
	 */
	public function doJob(JobInstance $jobInstance, JobExecution $jobExecution) {
		$this->initialize();
		return parent::doJob($jobInstance, $jobExecution);
	}

}