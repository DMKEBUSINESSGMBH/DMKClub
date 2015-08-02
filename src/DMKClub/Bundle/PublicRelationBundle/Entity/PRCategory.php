<?php

namespace DMKClub\Bundle\PublicRelationBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\IntegrationBundle\Model\IntegrationEntityTrait;

use DMKClub\Bundle\PublicRelationBundle\Model\ExtendPRCategory;

/**
 * Class P/R Category
 *
 * @package DMKClub\Bundle\PublicRelationBundle\Entity
 *
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\PublicRelationBundle\Entity\Repository\PRCategoryRepository")
 * @ORM\Table(name="dmkclub_prcategory")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_prcategory_index",
 *      routeView="dmkclub_prcategory_view",
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
 *              "form_type"="dmkclub_prcategory_select",
 *              "grid_name"="dmkclub-prcategories-select-grid"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 * @Oro\Loggable
 * Die Angaben in "form" dienen dem create_select_form_inline
 */
class PRCategory extends ExtendPRCategory {
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
	 * @ORM\Column(name="name", type="string", length=255)
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
	 * @var User
	 * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
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
	 * Set id
	 *
	 * @param int $id
	 * @return Category
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
	 * @return Category
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
	}

	/**
	 * Set organization
	 *
	 * @param Organization $organization
	 * @return Customer
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
}
