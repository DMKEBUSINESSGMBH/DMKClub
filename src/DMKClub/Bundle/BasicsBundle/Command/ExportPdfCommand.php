<?php
namespace DMKClub\Bundle\BasicsBundle\Command;

use Doctrine\ORM\Query;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

use Oro\Bundle\CronBundle\Command\CronCommandInterface;

use Akeneo\Bundle\BatchBundle\Job\BatchStatus;
use Akeneo\Bundle\BatchBundle\Job\DoctrineJobRepository;
use Doctrine\ORM\EntityManager;
use Akeneo\Bundle\BatchBundle\Entity\JobExecution;
use DMKClub\Bundle\BasicsBundle\Job\JobExecutor;

/**
 * Erzeugung und Export von PDF Dateien.
 * - Suche nach JobInstanzen ohne Execution
 * - Start von Jobs
 * - Suche nach fehlerhaften Jobs und Neustart
 *
 * Es sollten nur neue Jobs gestartet werden, wenn es keine laufenden Prozesse gibt.
 *
 * @author "René Nitzsche"
 */
class ExportPdfCommand extends ContainerAwareCommand implements CronCommandInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultDefinition() {
		return '* * * * *';
	}
	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
		->setName('oro:cron:dmkclub:exportpdf')
		->setDescription('Process PDF export jobs');

	}

	/**
	 * {@inheritdoc}
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		$output->writeln('Search vor pdf exports',1);
		$output->writeln('======================',2);

		// Gibt es neue JobInstanzen ?

		$qb = $this->getJobManager()
			->getRepository('Akeneo\Bundle\BatchBundle\Entity\JobExecution')
			->createQueryBuilder('je');


		/* @var $query \Doctrine\ORM\Query  */
		$query = $qb
			->leftJoin('je.jobInstance', 'ji')
			->where($qb->expr()->eq('je.status', ':status'))
			->andWhere('ji.alias = :alias')
			->setParameter('status', BatchStatus::STARTING)
			->setParameter('alias', 'dmkexportpdf')
			->getQuery();

		$executor = $this->getJobExecutor();
		$inc = 0;
		$result = $query->iterate();
		foreach ($result as $jobExecution) {
			/* @var $jobExecution JobExecution */
			$jobExecution = reset($jobExecution);
			$output->writeln("\nStart job execution with id ".$jobExecution->getId());

			$jobResult = $executor->doJob($jobExecution->getJobInstance(), $jobExecution);
			$output->writeln(print_r(['result'=>($jobResult->isSuccessful() ? 'success' : 'error')],true));
			$inc++;
		}
		$output->writeln("\nFinished with ".$inc." jobs processed\n");

	}


	/**
	 * @return EntityManager
	 */
	protected function getJobManager() {
		/* @var $repo DoctrineJobRepository */
		$repo = $this->getContainer()->get('akeneo_batch.job_repository');
		return $repo->getJobManager();
	}
	/**
	 * @return JobExecutor
	 */
	protected function getJobExecutor() {
		return $this->getContainer()->get('dmkclub_basics.job_executor');
	}
}
