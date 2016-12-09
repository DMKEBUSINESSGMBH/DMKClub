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
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberProposal;
use Oro\Bundle\TagBundle\Entity\Taggable;
use Oro\Bundle\AddressBundle\Entity\Address;
use OroCRM\Bundle\ChannelBundle\Model\ChannelAwareInterface;
use OroCRM\Bundle\ChannelBundle\Model\ChannelEntityTrait;
use OroCRM\Bundle\ChannelBundle\Model\CustomerIdentityInterface;

/**
 * Class MemberProposal
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\MemberBundle\Entity\Repository\MemberProposalRepository")
 * @ORM\Table(name="dmkclub_member_proposal")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_memberproposal_index",
 *      routeView="dmkclub_memberproposal_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-envelope"
 *          },
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="",
 *              "category"="dmkclub_data"
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
 */
class MemberProposal extends ExtendMemberProposal implements ChannelAwareInterface {
	use ChannelEntityTrait, LifecycleTrait;

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
	 * @ORM\Column(type="string", length=255, nullable=true)
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
	protected $firstname;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @Soap\ComplexType("string")
	 * @Oro\Versioned
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=31
	 *          }
	 *      }
	 * )
	 */
	protected $lastname;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
	 */
	protected $emailAddress;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone", type="string", length=100, nullable=true)
	 */
	protected $phone;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="comment", type="text")
	 */
	protected $comment;

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
	 * @var WorkflowItem
	 *
	 * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
	 * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $workflowItem;

	/**
	 * @var WorkflowStep
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
	 * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $workflowStep;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;


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
	 * @param string $name
	 * @return Member
	 */
	public function setFirstname($name)
	{
		$this->firstname = $name;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @param string $name
	 * @return Member
	 */
	public function setLastname($name)
	{
		$this->lastname = $name;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @param string $emailAddress
	 */
	public function setEmailAddress($emailAddress)
	{
		$this->emailAddress = $emailAddress;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress()
	{
		return $this->emailAddress;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
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
	 * @return User
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param Organization $organization
	 */
	public function setOwner(Organization $organization) {
		$this->owner = $organization;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
	    return (string) $this->getName();
	}
}
