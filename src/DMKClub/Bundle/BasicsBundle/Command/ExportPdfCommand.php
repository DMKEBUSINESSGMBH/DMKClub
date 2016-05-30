<?php
namespace DMKClub\Bundle\BasicsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Oro\Bundle\CronBundle\Command\CronCommandInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\Query;
use Akeneo\Bundle\BatchBundle\Job\BatchStatus;

class ExportPdfCommand extends ContainerAwareCommand implements CronCommandInterface {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultDefinition() {
		return '1 * * * *';
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
		$output->writeln('Hello Job');

		/* @var $repo DoctrineJobRepository */
		$repo = $this->getContainer()->get('akeneo_batch.job_repository');
		$manager = $repo->getJobManager();

		$qb = $manager
			->getRepository('Akeneo\Bundle\BatchBundle\Entity\JobExecution')
			->createQueryBuilder('je');

		/* @var $query \Doctrine\ORM\Query  */
		$query = $qb
			->select('COUNT(je) as jobs')
			->leftJoin('je.jobInstance', 'ji')
			->where($qb->expr()->lt('je.status', ':status'))
			->setParameter('status', BatchStatus::FAILED)
			->getQuery();

		$dql = $query->getDQL();
		$output->writeln("\n----\n$dql\n\n");
		$params = $query->getParameters();
		$output->writeln("\n----\n".print_r($params->toArray(),true)."\n\n");

	}

}
