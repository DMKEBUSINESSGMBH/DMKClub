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
 *              "group_name"=""
 *          },
 *          "form"={
 *              "grid_name"="dmkclub-members-grid",
 *              "form_type"="dmkclub_member_select"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 * @Oro\Loggable
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
class Member extends ExtendMember implements Taggable, ChannelAwareInterface, CustomerIdentityInterface {
    use ChannelEntityTrait;
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
     *              "order"=10
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
     *              "order"=80
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
     *              "identity"=true,
     *              "order"=30
     *          }
     *      }
     * )
     */
    protected $name;


    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(type="datetime", name="created_at")
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(type="datetime", name="updated_at")
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    /**
     * @var Contact
     *
     * @ORM\ManyToOne(targetEntity="OroCRM\Bundle\ContactBundle\Entity\Contact", cascade="PERSIST")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $contact;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_honorary")
     */
    protected $isHonorary = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_free_of_charge")
     */
    protected $isFreeOfCharge = false;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_option", type="string", length=20, nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $paymentOption;

    /**
     * @var Address $postalAddress
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\AddressBundle\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="postal_address", referencedColumnName="id", onDelete="SET NULL")
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "full"=true,
     *              "order"=20
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
     */
    protected $account;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;
    /**
     * @var ArrayCollection
     */
    protected $tags;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

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
     * @param bool $isFreeOfCharge
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
     * @param bool $isFreeOfCharge
     *
     * @return Member
     */
    public function setIsFreeOfCharge($isFreeOfCharge)
    {
    	$this->isFreeOfCharge = $isFreeOfCharge;

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
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $user
     */
    public function setOwner(User $user)
    {
        $this->owner = $user;
        return $this;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     * @return Member
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
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
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $this->tags = new ArrayCollection();
        }

        return $this->tags;
    }

    /**
     * @param $tags
     * @return Contact
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * Pre update event handler
     *
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}
