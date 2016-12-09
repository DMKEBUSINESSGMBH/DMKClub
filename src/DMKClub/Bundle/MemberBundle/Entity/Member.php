<?php

namespace DMKClub\Bundle\MemberBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\IntegrationBundle\Model\IntegrationEntityTrait;

use OroCRM\Bundle\ContactBundle\Entity\Contact;

use DMKClub\Bundle\BasicsBundle\Model\LifecycleTrait;
use DMKClub\Bundle\MemberBundle\Model\ExtendMember;
use Oro\Bundle\TagBundle\Entity\Taggable;
use Oro\Bundle\AddressBundle\Entity\Address;
use OroCRM\Bundle\ChannelBundle\Model\ChannelAwareInterface;
use OroCRM\Bundle\ChannelBundle\Model\ChannelEntityTrait;
use OroCRM\Bundle\ChannelBundle\Model\CustomerIdentityInterface;

/**
 * Class Member
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository")
 * @ORM\Table(name="dmkclub_member")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_member_index",
 *      routeView="dmkclub_member_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-user-md"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="user_owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="",
 *              "category"="dmkclub_data"
 *          },
 *          "form"={
 *              "grid_name"="dmkclub-members-grid",
 *              "form_type"="dmkclub_member_select"
 *          },
 *          "tag"={
 *              "enabled"=true
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 * @Oro\Loggable
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
class Member extends ExtendMember implements ChannelAwareInterface, CustomerIdentityInterface {
	use ChannelEntityTrait, LifecycleTrait;
	/*
	 * Fields have to be duplicated here to enable dataaudit and soap transformation only for contact
	*/
	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Soap\ComplexType("int", nillable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "importexport"={
	 *              "order"=10
	 *          }
	 *      }
	 * )
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="member_code", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "identity"=true,
	 *              "order"=20
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $memberCode;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="start_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=75
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $startDate;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="end_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=77
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $endDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 * @Soap\ComplexType("string")
	 * @Oro\Versioned
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=30
	 *          }
	 *      }
	 * )
	 */
	protected $name;

	/**
	 * @ORM\OneToMany(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberFee", mappedBy="member", cascade={"all"}, orphanRemoval=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={"auditable"=true},
	 *          "importexport"={"excluded"=true}
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	private $memberFees;

	/**
	 * @ORM\OneToMany(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount", mappedBy="member", cascade={"all"}, orphanRemoval=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={"auditable"=true},
	 *          "importexport"={"excluded"=true}
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	private $memberFeeDiscounts;


	/**
	 * @var Contact
	 *
	 * @ORM\ManyToOne(targetEntity="OroCRM\Bundle\ContactBundle\Entity\Contact", cascade="PERSIST")
	 * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=500,
	 *              "full"=true
	 *          }
	 *      }
	 * )
	 */
	protected $contact;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", name="is_active", options={"default" : false})
	 * @Oro\Versioned
	 * @ConfigField(
	 * 	defaultValues={"dataaudit"={"auditable"=true},
	 *          "importexport"={
	 *              "order"=40
	 *          }
	 *  }
	 * )
	 */
	protected $isActive = false;


	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", name="is_honorary", options={"default" : false})
	 * @Oro\Versioned
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true},
	 *          "importexport"={
	 *              "order"=50
	 *          }
	 *   }
	 * )
	 */
	protected $isHonorary = false;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", name="is_free_of_charge", options={"default" : false})
	 * @Oro\Versioned
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true},
	 *          "importexport"={
	 *              "order"=60
	 *          }
	 *   }
	 * )
	 */
	protected $isFreeOfCharge = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="status", type="string", length=20, nullable=true, options={"default" : "active"})
	 * @Soap\ComplexType("string", nillable=true)
	 * @Oro\Versioned
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=70
	 *          }
	 *      }
	 * )
	 */
	protected $status;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="payment_option", type="string", length=20, nullable=true, options={"default" : "none"})
	 * @Soap\ComplexType("string", nillable=true)
	 * @Oro\Versioned
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=80
	 *          }
	 *      }
	 * )
	 */
	protected $paymentOption;

	/**
	 * @var Account
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\PaymentBundle\Entity\BankAccount", cascade="PERSIST")
	 * @ORM\JoinColumn(name="bank_account", referencedColumnName="id", onDelete="SET NULL")
	 * @Oro\Versioned
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "full"=true,
	 *              "order"=85
	 *          }
	 *      }
	 * )
	 */
	protected $bankAccount;

	/**
	 * @var Address $postalAddress
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\AddressBundle\Entity\Address", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="postal_address", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "importexport"={
	 *              "full"=true,
	 *              "order"=150
	 *          }
	 *      }
	 * )
	 */
	protected $postalAddress;

	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $owner;

	/**
	 * @var Account
	 *
	 * @ORM\ManyToOne(targetEntity="OroCRM\Bundle\AccountBundle\Entity\Account", cascade="PERSIST")
	 * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "importexport"={
	 *              "full"=false,
	 *              "order"=450
	 *          }
	 *      }
	 * )
	 */
	protected $account;

	/**
	 * @var Organization
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "importexport"={
	 *              "full"=false,
	 *              "order"=460
	 *          }
	 *      }
	 * )
	 */
	protected $organization;

	/**
	 * {@inheritdoc}
	 */
	public function __construct()
	{
	    parent::__construct();
	    $this->memberFees = new \Doctrine\Common\Collections\ArrayCollection();
	    $this->memberFeeDiscounts = new \Doctrine\Common\Collections\ArrayCollection();

	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $endDate
	 * @return Member
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $endDate
	 * @return Member
	 */
	public function setName($id)
	{
		$this->name = $id;

		return $this;
	}

	/**
	 * Get id
	 *
	 * @return int
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param Contact $contact
	 *
	 * @return Member
	 */
	public function setContact($contact)
	{
	    $this->contact = $contact;

	    return $this;
	}

	/**
	 * @return Contact
	 */
	public function getContact()
	{
	    return $this->contact;
	}

	/**
	 * @param bool $isHonorary
	 *
	 * @return Member
	 */
	public function setIsHonorary($isHonorary)
	{
		$this->isHonorary = $isHonorary;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsHonorary()
	{
		return $this->isHonorary;
	}

	/**
	 * @param bool $flag
	 *
	 * @return Member
	 */
	public function setIsFreeOfCharge($flag)
	{
		$this->isFreeOfCharge = $flag;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsFreeOfCharge()
	{
		return $this->isFreeOfCharge;
	}

	/**
	 * @param bool $isActive
	 *
	 * @return Customer
	 */
	public function setIsActive($isActive)
	{
	    $this->isActive = $isActive;

	    return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsActive()
	{
	    return $this->isActive;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $value
	 * @return Member
	 */
	public function setStatus($value) {
		$this->status = $value;
	  return $this;
	}

	/**
	 * @return string
	 */
	public function getPaymentOption() {
		return $this->paymentOption;
	}

	/**
	 * @param string $value
	 * @return Member
	 */
	public function setPaymentOption($value) {
		$this->paymentOption = $value;
		return $this;
	}

	/**
	 * @return \DMKClub\Bundle\PaymentBundle\Entity\BankAccount
	 */
	public function getBankAccount() {
		return $this->bankAccount;
	}

	/**
	 *
	 * @param \DMKClub\Bundle\PaymentBundle\Entity\BankAccount $value
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Member
	 */
	public function setBankAccount($value) {
		$this->bankAccount = $value;
		return $this;
	}

	/**
	 * @return Address
	 */
	public function getPostalAddress()
	{
		return $this->postalAddress;
	}

	/**
	 * @param Address $address
	 */
	public function setPostalAddress(Address $address)
	{
		$this->postalAddress = $address;
	  return $this;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection [\DMKClub\Bundle\MemberBundle\Entity\MemberFee]
	 */
	public function getMemberFees() {
		return $this->memberFees;
	}

	/**
	 * @param MemberFee[] $memberFees
	 */
	public function setMemberFees($memberFees) {
		foreach ($memberFees as $memberFee) {
			$memberFee->setMember($this);
		}
		$this->memberFees = $memberFees;
	}

	/**
	 * Add member fee
	 *
	 * @param MemberFee $memberFee
	 * @return Member
	 * @internal param MemberFee $memberFees
	 */
	public function addMemberFee(MemberFee $memberFee) {
		$memberFee->setMember($this);
		$this->memberFees[] = $memberFee;
		return $this;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection [\DMKClub\Bundle\MemberBundle\Entity\MemberFee]
	 */
	public function getMemberFeeDiscounts() {
		return $this->memberFeeDiscounts;
	}

	/**
	 * @param MemberFeeDiscount[] $memberFeeDiscounts
	 */
	public function setMemberFeeDiscounts($memberFeeDiscounts) {
		foreach ($memberFeeDiscounts as $memberFeeDiscount) {
			$memberFeeDiscount->setMember($this);
		}
		$this->memberFeeDiscounts = $memberFeeDiscounts;
	}

	/**
	 * Add member fee
	 *
	 * @param MemberFeeDiscount $memberFeeDiscount
	 * @return Member
	 * @internal param MemberFeeDiscount $memberFeeDiscounts
	 */
	public function addMemberFeeDiscount(MemberFeeDiscount $memberFeeDiscount) {
		$memberFeeDiscount->setMember($this);
		$this->memberFeeDiscounts[] = $memberFeeDiscount;
		return $this;
	}

	/**
	 * @return User
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param User $user
	 */
	public function setOwner(User $user) {
		$this->owner = $user;
		return $this;
	}

	/**
	 * Set organization
	 *
	 * @param Organization $organization
	 * @return Member
	 */
	public function setOrganization(Organization $organization = null) {
		$this->organization = $organization;
		return $this;
	}

	/**
	 * Get organization
	 *
	 * @return Organization
	 */
	public function getOrganization() {
		return $this->organization;
	}

	/**
	 * @param Account $account
	 *
	 * @return Member
	 */
	public function setAccount($account) {
		$this->account = $account;

		return $this;
	}

	/**
	 * @return Account
	 */
	public function getAccount() {
		return $this->account;
	}

	/**
	 * Set memberCode
	 *
	 * @param string $memberCode
	 * @return Member
	 */
	public function setMemberCode($memberCode)
	{
	    $this->memberCode = $memberCode;

	    return $this;
	}

	/**
	 * Get memberCode
	 *
	 * @return string
	 */
	public function getMemberCode()
	{
	    return $this->memberCode;
	}

	/**
	 * Set startDate
	 *
	 * @param \DateTime $startDate
	 * @return Member
	 */
	public function setStartDate($startDate)
	{
	    $this->startDate = $startDate;

	    return $this;
	}

	/**
	 * Get startDate
	 *
	 * @return \DateTime
	 */
	public function getStartDate()
	{
	    return $this->startDate;
	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $endDate
	 * @return Member
	 */
	public function setEndDate($endDate)
	{
	    $this->endDate = $endDate;

	    return $this;
	}

	/**
	 * Get endDate
	 *
	 * @return \DateTime
	 */
	public function getEndDate()
	{
	    return $this->endDate;
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
	    return (string) $this->getName();
	}
}
