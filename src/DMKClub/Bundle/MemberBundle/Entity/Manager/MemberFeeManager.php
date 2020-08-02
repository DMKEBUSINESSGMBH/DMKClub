<?php
namespace DMKClub\Bundle\MemberBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

class MemberFeeManager implements ContainerAwareInterface
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->setContainer($container);
    }

    /**
     * Set correction status for MemberFee
     *
     * @param MemberFee $entity
     * @param bool $isSeen
     * @param bool $flush
     *            - if true then method executes flush
     */
    public function setFeeCorrectionStatus(MemberFee $entity, $enableCorrection = true, $flush = false)
    {
        if ($entity->getCorrectionStatus() !== $enableCorrection) {
            $entity->setCorrectionStatus($enableCorrection ? MemberFee::CORRECTION_STATUS_OPEN : MemberFee::CORRECTION_STATUS_NONE);
            if ($flush) {
                $this->em->flush();
            }
        }
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container
     *            A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
     */
    public function getMemberRepository()
    {
        return $this->em->getRepository('DMKClubMemberBundle:Member');
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
     */
    public function getMemberFeeRepository()
    {
        return $this->em->getRepository('DMKClubMemberBundle:MemberFee');
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberBillingRepository
     */
    public function getMemberBillingRepository()
    {
        return $this->em->getRepository('DMKClubMemberBundle:MemberBilling');
    }
}
