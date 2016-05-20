<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Model\Processor;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use DMKClub\Bundle\MemberBundle\Accounting\AccountingException;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use Oro\Bundle\SegmentBundle\Entity\SegmentType;
use Oro\Bundle\SegmentBundle\Query\StaticSegmentQueryBuilder;
use Oro\Bundle\SegmentBundle\Query\DynamicSegmentQueryBuilder;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

class MemberFeeManager implements ContainerAwareInterface {
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	public function __construct(EntityManager $em, ContainerInterface $container) {
		$this->em = $em;
		$this->setContainer($container);
	}
	/**
	 * Set correction status for MemberFee
	 *
	 * @param MemberFee $entity
	 * @param bool      $isSeen
	 * @param bool      $flush - if true then method executes flush
	 */
	public function setFeeCorrectionStatus(MemberFee $entity, $enableCorrection = true, $flush = false) {
		if ($entity->getCorrectionStatus() !== $enableCorrection) {
			$entity->setCorrectionStatus(MemberFee::CORRECTION_STATUS_OPEN);
			if ($flush) {
				$this->em->flush();
			}
		}
	}

	/**
	 * Sets the Container.
	 *
	 * @param ContainerInterface|null $container A ContainerInterface instance or null
	 *
	 * @api
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
	 */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}
	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
	 */
	public function getMemberFeeRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:MemberFee');
	}
}
