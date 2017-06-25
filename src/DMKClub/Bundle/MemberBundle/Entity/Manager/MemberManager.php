<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Manager;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use OroCRM\Bundle\ContactBundle\Entity\Contact;
use OroCRM\Bundle\ContactBundle\Entity\ContactEmail;
use OroCRM\Bundle\ContactBundle\Entity\ContactPhone;
use Oro\Bundle\AddressBundle\Entity\Address;
use DMKClub\Bundle\PaymentBundle\Entity\BankAccount;
use DMKClub\Bundle\MemberBundle\Model\MemberStatus;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;

class MemberManager implements ContainerAwareInterface {
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
	 * The next possible memberCode
	 * @return number
	 */
	public function nextMemberCode()
	{
	    $qb = $this->getMemberRepository()->createQueryBuilder('m');
	    $qb->select('max(m.memberCodeInt) mc');
	    $data = $qb->getQuery()->getArrayResult();
	    $data = reset($data);
	    return (int) ($data['mc'] + 1);
	}
	/**
	 *
	 * @param MemberProposal $entity
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Member
	 */
	public function buildMemberByProposal(MemberProposal $entity)
	{
	    $member = new Member();
        $contact = new Contact();
        // contact
        if($entity->getEmail()) {
            $email = new ContactEmail($entity->getEmail());
            $email->setPrimary(true);
            $contact->addEmail($email);
        }
        if($entity->getPhone()) {
            $phone = new ContactPhone($entity->getPhone());
            $phone->setPrimary(true);
            $contact->addPhone($phone);
        }
        if($proposalAddress = $entity->getPostalAddress()) {
            $address = new Address();
            $address->setCity($entity->getPostalAddress()->getCity());
            $address->setCountry($entity->getPostalAddress()->getCountry());

            $useMemberName = (!$proposalAddress->getFirstName() && !$proposalAddress->getLastName());
            $address->setFirstName(
                $useMemberName ? $entity->getFirstName() : $proposalAddress->getFirstName()
            );
            $address->setLabel($entity->getPostalAddress()->getLabel());
            $address->setLastName(
                $useMemberName ? $entity->getLastName() : $proposalAddress->getLastName()
            );
            $address->setMiddleName(
                $useMemberName ? $entity->getMiddleName() : $proposalAddress->getMiddleName()
            );
            $address->setNamePrefix(
                $useMemberName ? $entity->getNamePrefix() : $proposalAddress->getNamePrefix()
            );
            $address->setNameSuffix(
                $useMemberName ? $entity->getNameSuffix() : $proposalAddress->getNameSuffix()
            );
            $address->setPostalCode($entity->getPostalAddress()->getPostalCode());
            $address->setRegion($entity->getPostalAddress()->getRegion());
            $address->setRegionText($entity->getPostalAddress()->getRegionText());
            $address->setStreet($entity->getPostalAddress()->getStreet());
            $address->setStreet2($entity->getPostalAddress()->getStreet2());
            $address->setCity($entity->getPostalAddress()->getCity());

            $member->setPostalAddress($address);
        }
        if($entity->getBankAccount()) {
            $bankAccount = new BankAccount();
            $bankAccount->setAccountOwner($entity->getBankAccount()->getAccountOwner());
            $bankAccount->setBankName($entity->getBankAccount()->getBankName());
            $bankAccount->setBic($entity->getBankAccount()->getBic());
            $bankAccount->setIban($entity->getBankAccount()->getIban());
            $bankAccount->setDirectDebitValidFrom($entity->getBankAccount()->getDirectDebitValidFrom());

            $member->setBankAccount($bankAccount);
        }
        if ($entity->getDiscountStartDate()) {
            $feeDiscount = new MemberFeeDiscount();
            $feeDiscount->setStartDate($entity->getDiscountStartDate());
            $feeDiscount->setEndDate($entity->getDiscountEndDate());
            $feeDiscount->setReason($entity->getDiscountReason());

            $member->addMemberFeeDiscount($feeDiscount);
        }
        $contact
            ->setBirthday($entity->getBirthday())
            ->setFirstName($entity->getFirstName())
            ->setLastName($entity->getLastName())
            ->setJobTitle($entity->getJobTitle())
            ->setMiddleName($entity->getMiddleName())
            ->setNamePrefix($entity->getNamePrefix())
            ->setNameSuffix($entity->getNameSuffix());

        $member->setContact($contact);
        $member->setStatus(MemberStatus::ACTIVE);
        $member->setPaymentOption($entity->getPaymentOption());
        $member->setPaymentInterval($entity->getPaymentInterval());
        $member->setIsActive($entity->getIsActive());
        $member->setDataChannel($entity->getDataChannel());
        $member->setName($contact->getFirstName() . ' ' . $contact->getLastName());

        return $member;
    }

    /**
     *
     * @return MemberRepository
     */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}
	/**
	 * Sets the Container.
	 *
	 * @param ContainerInterface|null $container
	 *            A ContainerInterface instance or null
	 *
	 *            @api
	 */
	public function setContainer(ContainerInterface $container = null)
	{
	    $this->container = $container;
	}
}
