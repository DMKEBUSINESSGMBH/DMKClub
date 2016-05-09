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
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberFee;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberFeePosition;
use OroCRM\Bundle\MagentoBundle\Entity\OriginTrait;

/**
 * Class MemberFeePosition
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="dmkclub_member_feeposition")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-list-alt"
 *          }
 *      }
 * )
 */
class MemberFeePosition extends ExtendMemberFeePosition {

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Soap\ComplexType("int", nillable=true)
	 */
	protected $id;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="quantity", type="float", nullable=true)
	 */
	private $quantity;
	/**
	 * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberFee", inversedBy="positions")
	 * @ORM\JoinColumn(name="member_fee", referencedColumnName="id", onDelete="CASCADE")
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 * @Oro\Versioned
	 */
	private $memberFee;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="unit", type="string", length=255, nullable=true)
	 */
	private $unit;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	private $description;
	/**
	 * @var float
	 *
	 * @ORM\Column(name="price_single", type="integer", nullable=true)
	 */
	private $priceSingle;
	/**
	 * @var float
	 *
	 * @ORM\Column(name="price_total", type="integer", nullable=true)
	 */
	private $priceTotal;
	/**
	 * @var double
	 *
	 * @ORM\Column(name="tax_amount", type="integer", nullable=true)
	 */
	private $taxAmount;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="sort_order", type="integer", nullable=true)
	 */
	private $sortOrder;


	public function getTaxAmount() {
	  return $this->taxAmount;
	}

	public function setTaxAmount($value) {
	  $this->taxAmount = $value;
	  return $this;
	}

	public function getPriceTotal() {
	  return $this->priceTotal;
	}

	public function setPriceTotal($value) {
	  $this->priceTotal = $value;
	  return $this;
	}


	public function getSortOrder() {
	  return $this->sortOrder;
	}

	public function setSortOrder($value) {
	  $this->sortOrder = $value;
	  return $this;
	}

	public function getPriceSingle() {
	  return $this->priceSingle;
	}

	public function setPriceSingle($value) {
	  $this->priceSingle = $value;
	  return $this;
	}

	//  single_price, total_price

	public function getDescription() {
	  return $this->description;
	}

	public function setDescription($value) {
	  $this->description = $value;
	  return $this;
	}


	public function getUnit() {
	  return $this->unit;
	}

	public function setUnit($value) {
	  $this->unit = $value;
	  return $this;
	}


	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\MemberFee
	 */
	public function getMemberFee() {
	  return $this->memberFee;
	}

	/**
	 *
	 * @param \DMKClub\Bundle\MemberBundle\Entity\MemberFee $value
	 * @return MemberFeePosition
	 */
	public function setMemberFee(\DMKClub\Bundle\MemberBundle\Entity\MemberFee $value) {
	  $this->memberFee = $value;
	  return $this;
	}


	/**
	 * @return int
	 */
	public function getQuantity() {
	  return $this->quantity;
	}

	public function setQuantity($value) {
	  $this->quantity = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Set endDate
	 *
	 * @param \DateTime $endDate
	 * @return Member
	 */
	public function setId($id) {
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
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->getId();
	}


}
