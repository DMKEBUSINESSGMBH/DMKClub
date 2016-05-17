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

use DMKClub\Bundle\MemberBundle\Model\ExtendMemberFeeDiscount;

/**
 * Class MemberFeeDiscount
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="dmkclub_member_feediscount")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-list-alt"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
class MemberFeeDiscount extends ExtendMemberFeeDiscount {

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
	 * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\Member", inversedBy="memberFeeDiscounts")
	 * @ORM\JoinColumn(name="member", referencedColumnName="id", onDelete="CASCADE")
	 * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
	 * @Oro\Versioned
	 */
	private $member;


	/**
	 * @var \Date
	 *
	 * @ORM\Column(name="start_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $startDate;
	/**
	 * @var \Date
	 *
	 * @ORM\Column(name="end_date", type="date", nullable=true)
	 * @ConfigField(
	 *      defaultValues={
	 *          "dataaudit"={
	 *              "auditable"=true
	 *          }
	 *      }
	 * )
	 * @Oro\Versioned
	 */
	protected $endDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="reason", type="string", length=255, nullable=true)
	 */
	private $reason;

	//  single_price, total_price

	public function getReason() {
	  return $this->reason;
	}

	/**
	 *
	 * @param string $value
	 * @return \DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount
	 */
	public function setReason($value) {
	  $this->reason = $value;
	  return $this;
	}




	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Member
	 */
	public function getMember() {
	  return $this->member;
	}

	/**
	 *
	 * @param \DMKClub\Bundle\MemberBundle\Entity\Member $value
	 * @return MemberFeeDiscount
	 */
	public function setMember(\DMKClub\Bundle\MemberBundle\Entity\Member $value) {
	  $this->member = $value;
	  return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Set id
	 *
	 * @param int $endDate
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
	 * Set startDate
	 *
	 * @param \DateTime $startDate
	 * @return MemberFeeDiscount
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
	 * @return MemberFeeDiscount
	 */
	public function setEndDate($endDate) {
		$this->endDate = $endDate;

		return $this;
	}

	/**
	 * Get endDate
	 *
	 * @return \DateTime
	 */
	public function getEndDate() {
		return $this->endDate;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->getId();
	}


}
