<?php

namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\AddressBundle\Entity\Address;
use Oro\Bundle\LocaleBundle\Model\FullNameInterface;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\ChannelBundle\Model\ChannelEntityTrait;

use DMKClub\Bundle\BasicsBundle\Model\LifecycleTrait;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberProposal;
use Oro\Bundle\ChannelBundle\Model\ChannelAwareInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

/**
 * Class MemberProposal
 *
 * @ORM\Table(name="dmkclub_member_proposal")
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\MemberBundle\Entity\Repository\MemberProposalRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_member_proposal_index",
 *      routeView="dmkclub_member_proposal_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-envelope"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"="",
 *              "category"="dmkclub_data"
 *          },
 *          "comment"={
 *              "enabled"=true
 *          },
 *          "tag"={
 *              "enabled"=true
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
class MemberProposal extends ExtendMemberProposal implements
    ChannelAwareInterface,
    FullNameInterface,
    EmailHolderInterface
    {
	use ChannelEntityTrait, LifecycleTrait;

	const INTERNAL_STATUS_CODE = 'memberproposal_status';

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
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
	 * @ORM\Column(name="name_prefix", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true},
	 *      "importexport"={
	 *          "order"=30
	 *      }
	 *  }
	 * )
	 */
	protected $namePrefix;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=40
	 *          }
	 *      }
	 * )
	 */
	protected $firstName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true},
	 *      "importexport"={
	 *          "order"=50
	 *      }
	 *  }
	 * )
	 */
	protected $middleName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=60
	 *          }
	 *      }
	 * )
	 */
	protected $lastName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name_suffix", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true},
	 *      "importexport"={
	 *          "order"=70
	 *      }
	 *  }
	 * )
	 */
	protected $nameSuffix;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true}
	 *  }
	 * )
	 */
	protected $emailAddress;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone", type="string", length=100, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true}
	 *  }
	 * )

	 */
	protected $phone;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="birthday", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=80
	 *          },
	 *          "merge"={
	 *              "display"=true
	 *          }
	 *      }
	 * )
	 */
	protected $birthday;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="comment", type="text", nullable=true)
	 */
	protected $comment;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean", name="is_active", options={"default" : false})
	 * @ConfigField(
	 * 	defaultValues={"dataaudit"={"auditable"=true},
	 *          "importexport"={
	 *              "order"=100
	 *          }
	 *  }
	 * )
	 */
	protected $isActive = false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="job_title", type="string", length=255, nullable=true)
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true},
	 *      "importexport"={
	 *          "order"=130
	 *      }
	 *  }
	 * )
	 */
	protected $jobTitle;


	/**
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\MemberBundle\Entity\MemberProposalBankAccount", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="bank_account", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "full"=true,
	 *              "order"=140
	 *          }
	 *      }
	 * )
	 */
	protected $bankAccount;

	/**
	 * @var Address $postalAddress
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\MemberBundle\Entity\MemberProposalAddress", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="postal_address", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "full"=true,
	 *              "order"=150
	 *          }
	 *      }
	 * )
	 */
	protected $postalAddress;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="discount_start_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          }
	 *      }
	 * )
	 */
	protected $discountStartDate;
	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="discount_end_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          }
	 *      }
	 * )
	 */
	protected $discountEndDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="discount_reason", type="string", length=255, nullable=true)
	 */
	protected $discountReason;

	/**
	 * @var Member
	 *
	 * @ORM\ManyToOne(targetEntity="DMKClub\Bundle\MemberBundle\Entity\Member", inversedBy="memberProposals")
	 * @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=false},
	 *      "importexport"={
	 *          "order"=160,
	 *          "short"=true
	 *      }
	 *  }
	 * )
	 */
	protected $member;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 * @ConfigField(
	 *      defaultValues={
	 *          "entity"={
	 *              "label"="oro.ui.created_at"
	 *          },
	 *          "importexport"={
	 *              "excluded"=true
	 *          }
	 *      }
	 * )
	 */
	protected $createdAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "entity"={
	 *              "label"="oro.ui.updated_at"
	 *          },
	 *          "importexport"={
	 *              "excluded"=true
	 *          }
	 *      }
	 * )
	 */
	protected $updatedAt;


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
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
	 * @ConfigField(
	 *  defaultValues={
	 *      "dataaudit"={"auditable"=true},
	 *      "importexport"={
	 *          "order"=180,
	 *          "short"=true
	 *      }
	 *  }
	 * )
	 */
	protected $owner;

	/**
	 * @var Organization
	 *
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
	 * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $organization;


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
	 * @return MemberProposal
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
	 * @param string $namePrefix
	 *
	 * @return MemberProposal
	 */
	public function setNamePrefix($namePrefix)
	{
	    $this->namePrefix = $namePrefix;

	    return $this;
	}

	/**
	 * @return string
	 */
	public function getNamePrefix()
	{
	    return $this->namePrefix;
	}

	/**
	 * @param string $name
	 * @return MemberProposal
	 */
	public function setFirstName($name)
	{
		$this->firstName = $name;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @return string
	 */
	public function getMiddleName()
	{
	    return $this->middleName;
	}

	/**
	 * @param string $middleName
	 *
	 * @return MemberProposal
	 */
	public function setMiddleName($middleName)
	{
	    $this->middleName = $middleName;

	    return $this;
	}


	/**
	 * @param string $name
	 * @return MemberProposal
	 */
	public function setLastName($name)
	{
		$this->lastName = $name;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param string $nameSuffix
	 *
	 * @return MemberProposal
	 */
	public function setNameSuffix($nameSuffix)
	{
	    $this->nameSuffix = $nameSuffix;

	    return $this;
	}

	/**
	 * @return string
	 */
	public function getNameSuffix()
	{
	    return $this->nameSuffix;
	}

	/**
	 * Set job title
	 *
	 * @param string $jobTitle
	 *
	 * @return MemberProposal
	 */
	public function setJobTitle($jobTitle)
	{
	    $this->jobTitle = $jobTitle;

	    return $this;
	}

	/**
	 * Get job title
	 *
	 * @return string
	 */
	public function getJobTitle()
	{
	    return $this->jobTitle;
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
	 * {@inheritDoc}
	 * @see \Oro\Bundle\EmailBundle\Model\EmailHolderInterface::getEmail()
	 */
	public function getEmail() {
	    return $this->getEmailAddress();
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
	 * @param \DateTime $birthday
	 *
	 * @return $this
	 */
	public function setBirthday($birthday)
	{
	    $this->birthday = $birthday;

	    return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getBirthday()
	{
	    return $this->birthday;
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
	 * @return MemberProposal
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
	 * @return MemberProposalBankAccount
	 */
	public function getBankAccount() {
		return $this->bankAccount;
	}

	/**
	 *
	 * @param MemberProposalBankAccount $value
	 * @return MemberProposal
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
	public function setPostalAddress(MemberProposalAddress $address)
	{
		$this->postalAddress = $address;
        return $this;
	}

	public function getDiscountStartDate()
	{
	    return $this->discountStartDate;
	}

	public function setDiscountStartDate($discountStartDate)
	{
	    $this->discountStartDate = $discountStartDate;
	    return $this;
	}

	public function getDiscountEndDate()
	{
	    return $this->discountEndDate;
	}

	public function setDiscountEndDate($discountEndDate)
	{
	    $this->discountEndDate = $discountEndDate;
	    return $this;
	}

	public function getDiscountReason()
	{
	    return $this->discountReason;
	}

	public function setDiscountReason($discountReason)
	{
	    $this->discountReason = $discountReason;
	    return $this;
	}

	/**
	 * @param Member $member
	 *
	 * @return MemberProposal
	 */
	public function setMember($member)
	{
	    $this->member = $member;

	    return $this;
	}

	/**
	 * @return Member
	 */
	public function getMember()
	{
	    return $this->member;
	}

	/**
	 * Set organization
	 *
	 * @param Organization $organization
	 * @return MemberProposal
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
	 * @return User
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param User $owningUser
	 *
	 * @return MemberProposal
	 */
	public function setOwner($owningUser)
	{
	    $this->owner = $owningUser;

	    return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
	    return (string) $this->getLastname() . ', ' . $this->getFirstname();
	}

}
