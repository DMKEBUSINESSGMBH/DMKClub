<?php

namespace DMKClub\Bundle\MemberBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

use DMKClub\Bundle\MemberBundle\Model\ExtendMemberFee;
use DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface;
use DMKClub\Bundle\BasicsBundle\Utility\Strings;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;

/**
 * Class MemberFee
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository")
 * @ORM\Table(name="dmkclub_member_fee")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_memberfee_index",
 *      routeView="dmkclub_memberfee_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-money"
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
class MemberFee extends ExtendMemberFee implements PdfAwareInterface, SepaDirectDebitAwareInterface {
	const CORRECTION_STATUS_NONE = 0;
	const CORRECTION_STATUS_OPEN = 1;
	const CORRECTION_STATUS_DONE = 2;

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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="bill_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          },
	 *          "importexport"={
	 *              "order"=85
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $billDate;

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
	 * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberBilling", inversedBy="memberFees")
	 * @ORM\JoinColumn(name="billing", referencedColumnName="id", onDelete="CASCADE")
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 * @Oro\Versioned
	 */
	protected $billing;

	/**
	 * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\Member", inversedBy="memberFees")
	 * @ORM\JoinColumn(name="member", referencedColumnName="id", onDelete="CASCADE")
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 * @Oro\Versioned
	 */
	protected $member;

	/**
	 * @ORM\OneToMany(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition", mappedBy="memberFee", cascade={"all"}, orphanRemoval=true)
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 * @Oro\Versioned
	 */
	protected $positions;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="price_total", type="integer", nullable=true)
	 */
	private $priceTotal;
	/**
	 *
	 * @var integer
	 *
	 * @ORM\Column(name="payed_total", type="integer", nullable=true)
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 */
	private $payedTotal;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="correction_status", type="integer", nullable=true)
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 */
	private $correctionStatus;


	/**
	 * @var \DateTime $createdAt
	 *
	 * @ORM\Column(type="datetime", name="created_at")
	 * @Oro\Versioned
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
	 * @var \DateTime $updatedAt
	 *
	 * @ORM\Column(type="datetime", name="updated_at")
	 * @Oro\Versioned
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
	public function __construct() {
	    parent::__construct();
	    $this->positions = new \Doctrine\Common\Collections\ArrayCollection();
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
	public function getPriceTotal() {
		return $this->priceTotal;
	}

	public function setPriceTotal($value) {
		$this->priceTotal = $value;
		return $this;
	}

	/**
	 * Calculate total fee from all positions
	 */
	public function updatePriceTotal() {
		$total = 0;
		foreach ($this->positions As $position) {
			$total += $position->getPriceTotal();
		}
		$this->setPriceTotal($total);
		return $this;
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Member
	 */
	public function getMember() {
		return $this->member;
	}
	public function setMember($member) {
		$this->member = $member;
		return $this;
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\MemberBilling
	 */
	public function getBilling() {
		return $this->billing;
	}
	public function setBilling($billing) {
		$this->billing = $billing;
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
	 * Set bill date
	 *
	 * @param \DateTime $date
	 * @return Member
	 */
	public function setBillDate($date)
	{
	    $this->billDate = $date;

	    return $this;
	}

	/**
	 * Get bill date
	 *
	 * @return \DateTime
	 */
	public function getBillDate()
	{
	    return $this->billDate;
	}

	public function getPositionsByFlag($flag) {
		$result = [];
		foreach ($this->positions As $position) {
			if($position->getFlag() == $flag) {
				$result[] = $position;
			}
		}
		return $result;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection [\DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition]
	 */
	public function getPositions() {
		return $this->positions;
	}

	/**
	 * @param MemberFeePosition[] $value
	 */
	public function setPositions($value) {
		$this->positions = $value;
	  return $this;
	}
	/**
	 * Add member fee position
	 *
	 * @param MemberFeePosition $position
	 * @return MemberFee
	 */
	public function addPosition(MemberFeePosition $position) {
		$position->setMemberFee($this);
		$this->positions[] = $position;
		return $this;
	}

	public function getPayedTotal() {
		return $this->payedTotal;
	}

	public function setPayedTotal($value) {
		$this->payedTotal = $value;
	}

	public function getCorrectionStatus() {
		return $this->correctionStatus;
	}

	public function setCorrectionStatus($value) {
		$this->correctionStatus = $value;
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
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface::getTemplate()
	 */
	public function getTemplate() {
		return $this->getBilling()->getTemplate();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface::getFilenamePrefix()
	 */
	public function getFilenamePrefix() {
		$prefix = [];
		$prefix[] = $this->getBilling()->getId();
		$prefix[] = $this->getId();
		$prefix[] = $this->getMember()->getName();
		$prefix = implode('_', $prefix);
		return Strings::sanitizeFilename($prefix);
	}
	/*
	 * (non-PHPdoc)
	 * @see \DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface::getExportFilesystem()
	 */
	public function getExportFilesystem() {
		return $this->getBilling()->getExportFilesystem();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\PDF\SepaDirectDebitAwareInterface::getSepaAmount()
	 */
	public function getSepaAmount() {
		return $this->getPriceTotal();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\PDF\SepaDirectDebitAwareInterface::getDebtorName()
	 */
	public function getDebtorName() {
		$name = $this->getMember()->getBankAccount()->getAccountOwner();
		return $name ? $name : $this->getMember()->getName();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\PDF\SepaDirectDebitAwareInterface::getDebtorBic()
	 */
	public function getDebtorBic() {
		return $this->getMember()->getBankAccount()->getBic();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\PDF\SepaDirectDebitAwareInterface::getDebtorIban()
	 */
	public function getDebtorIban() {
		return $this->getMember()->getBankAccount()->getIban();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\PDF\SepaDirectDebitAwareInterface::getDebtorMandateSignDate()
	 */
	public function getDebtorMandateSignDate() {
		return $this->getBillDate() ? $this->getBillDate() : new \DateTime();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface::getPaymentAware()
	 */
	public function getPaymentAware() {
		return $this->getBilling();
	}

	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface::getRemittanceInformation()
	 */
	public function getRemittanceInformation() {
		return $this->getBilling()->getName().' - ' .$this->getId();
	}

	/**
	 * (non-PHPdoc)
	 * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface::isSepaDirectDebitPossible()
	 */
	public function isSepaDirectDebitPossible() {
		return PaymentOption::SEPA_DIRECT_DEBIT === $this->getMember()->getPaymentOption();
	}

}
