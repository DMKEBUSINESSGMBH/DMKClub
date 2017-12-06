<?php
namespace DMKClub\Bundle\BasicsBundle\Job;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Oro\Bundle\ImportExportBundle\Job\JobExecutor as OroJobExecutor;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;

use JMS\JobQueueBundle\Entity\Job;

use Akeneo\Bundle\BatchBundle\Connector\ConnectorRegistry;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository as BatchJobRepository;
use Akeneo\Bundle\BatchBundle\Entity\JobExecution;
use Akeneo\Bundle\BatchBundle\Entity\JobInstance;

/**
 * Die Originalklasse wird überschrieben, um die Erstellung eine JobInstance von deren Ausführung zu trennen.
 * @deprecated
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
	public function createJob($jobType, $jobName, array $configuration, $scheduleCommand = FALSE) {
		$this->initialize();
		$jobInstance = $this->createJobInstance($jobType, $jobName, $configuration);
		$jobExecution = $this->createJobExecution($configuration, $jobInstance);
		if($scheduleCommand) {
			// Command für Ausführung anlegen
			$commandArgs = array('-vvv', $jobInstance->getCode(),);
			$command = 'akeneo:batch:job';
			$needFlush = true;
			$this->addJMSJob($command, $commandArgs, $needFlush);
		}
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


	/**
	 * Methode aus Oro\Bundle\NotificationBundle\Processor\AbstractNotificationProcessor
	 * Add command to job queue if it has not been added earlier
	 *
	 * @param string $command
	 * @param array $commandArgs
	 * @param boolean $needFlush
	 * @return boolean|integer
	 */
	protected function addJMSJob($command, $commandArgs = array(), $needFlush = false) {
		$this->entityManager->flush();
		$currJob = $this->entityManager->createQuery("SELECT j FROM JMSJobQueueBundle:Job j WHERE j.command = :command AND j.state <> :stateFinished AND j.state <> :stateFailed")
									->setParameter('command', $command)
									->setParameter('stateFinished', Job::STATE_FINISHED)
									->setParameter('stateFailed', Job::STATE_FAILED)
									->getResult();
		if (sizeof($currJob) == 0) {
			$job = new Job($command, $commandArgs);
			$this->entityManager->persist($job);
			if ($needFlush) {
				$this->entityManager->flush($job);
			}
		}

		return true;
	}

}