<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Model\Processor;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;

class MemberBillingManager implements ContainerAwareInterface {
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var ContainerInterface
	 */
	protected $container;
	protected $processionProvider;

	public function __construct(EntityManager $em, ContainerInterface $container, ProcessorProvider $processorProvider) {
		$this->em = $em;
		$this->setContainer($container);
		$this->processionProvider = $processorProvider;
	}

	/**
	 * Starts account process for given billing.
	 * Das muss später bestimmt mal asynchon gemacht werden. Jetzt aber zunächst die direkte Umsetzung.
	 *
	 * @param MemberBilling $entity
	 * @return array
	 */
	public function startAccounting(MemberBilling $entity) {
		/* @var $provider \DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider */
		$provider = $this->container->get('dmkclub_member.memberbilling.processorprovider');
		$processor = $provider->getProcessorByName($entity->getProcessor());

		$result = $processor->execute($entity, $this->getProcessorSettings($entity));

		return $result;
	}

	/**
	 * Return the configured options for selected processor
	 * @param MemberBilling $entity
	 * @return array
	 */
	public function getProcessorSettings(MemberBilling $entity) {
		// Hier müssen wir eingreifen. Die Storedaten sind serialisiert in der
		// processorConfig drin. Sie müssen in ein VO überführt und dann in
		// processorSetting gesetzt werden.
		// Beim Wechsel des processortypes muss man aber aufpassen, damit die Config noch passt!
		$data = $entity->getProcessorConfig();
		$data = $data ? unserialize($data) : [];
		return isset($data[$entity->getProcessor()]) ? $data[$entity->getProcessor()] : [];
	}

	/**
	 * Liefert die registrierten Prozessoren
	 * @return [Processor]
	 */
	public function getProcessors() {
		$this->processionProvider->getProcessors();
	}
	/**
	 * Sets the Container.
	 *
	 * @param ContainerInterface|null $container A ContainerInterface instance or null
	 *
	 * @api
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}
