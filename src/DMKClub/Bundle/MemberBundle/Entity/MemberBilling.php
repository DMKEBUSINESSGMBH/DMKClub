<?php
namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberBilling;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface;
use DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor;

/**
 * Class Billing
 *
 * @package DMKClub\Bundle\DMKClubMemberBundle\Entity
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\MemberBundle\Entity\Repository\MemberBillingRepository")
 * @ORM\Table(name="dmkclub_member_billing")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="dmkclub_memberbilling_index",
 *      routeView="dmkclub_memberbilling_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-briefcase"
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
 */
class MemberBilling extends ExtendMemberBilling implements SepaPaymentAwareInterface
{

    /**
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *      defaultValues={
     *      }
     * )
     */
    protected $id;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $startDate;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $endDate;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $name;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="sign", type="string", length=50, nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $sign;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="processor", type="string", length=255, nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $processor;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="processor_config", type="text", nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $processorConfig;

    protected $processorSettings;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="position_labels", type="text", nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $positionLabels = "FEE Fee from [STARTDATE] to [ENDDATE]
ADMISSION  admission fee
FEECORRECTION fee correction";

    /**
     *
     * @var string
     *
     * @ORM\Column(name="export_filesystem", type="string", length=255, nullable=true)
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    private $exportFilesystem;

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="fee_total", type="integer", nullable=true)
     */
    private $feeTotal;

    private $payedTotal;

    /**
     *
     * @var \DateTime $createdAt
     *
     * @ORM\Column(type="datetime", name="created_at")
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
     *
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(type="datetime", name="updated_at")
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
     *
     * @ORM\OneToMany(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\MemberFee", mappedBy="billing", cascade={"all"}, orphanRemoval=true)
     * @ConfigField(defaultValues={"dataaudit"={"auditable"=true}})
     */
    protected $memberFees;

    /**
     *
     * @var Segment
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\SegmentBundle\Entity\Segment")
     * @ORM\JoinColumn(name="segment_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $segment;

    /**
     *
     * @var SepaCreditor
     * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor")
     * @ORM\JoinColumn(name="creditor_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $sepaCreditor;

    /**
     *
     * @var \DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate
     * @ORM\ManyToOne(targetEntity="\DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $template;

    /**
     *
     * @var User
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     *
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     *
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->memberFees = new ArrayCollection();
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
     * Set name
     *
     * @param string $value
     * @return Member
     */
    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set processor
     *
     * @param string $value
     * @return Member
     */
    public function setProcessor($value)
    {
        $this->processor = $value;

        return $this;
    }

    /**
     * Get processor
     *
     * @return string
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set processorConfig
     *
     * @param string $value
     * @return Member
     */
    public function setProcessorConfig($value)
    {
        $this->processorConfig = $value;

        return $this;
    }

    /**
     * Get processorConfig
     *
     * @return string
     */
    public function getProcessorConfig()
    {
        return $this->processorConfig;
    }

    /**
     * Set processorConfig
     *
     * @param array $value
     * @return Member
     */
    public function setProcessorSettings($value)
    {
        $this->processorSettings = $value;

        return $this;
    }

    /**
     * Get processorSettings
     *
     * @return array
     */
    public function getProcessorSettings()
    {
        return $this->processorSettings;
    }

    public function getExportFilesystem()
    {
        return $this->exportFilesystem;
    }

    public function setExportFilesystem($value)
    {
        $this->exportFilesystem = $value;
        return $this;
    }

    public function getFeeTotal()
    {
        return $this->feeTotal;
    }

    /**
     * Set total fee
     *
     * @param integer $value
     * @return Member
     */
    public function setFeeTotal($value)
    {
        $this->feeTotal = $value;
        return $this;
    }

    /**
     *
     * @return ArrayCollection [\DMKClub\Bundle\MemberBundle\Entity\MemberFee]
     */
    public function getMemberFees()
    {
        return $this->memberFees;
    }

    /**
     *
     * @param MemberFee[] $deliveries
     */
    public function setMemberFees($memberFees)
    {
        foreach ($memberFees as $memberFee) {
            $memberFee->setBilling($this);
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
    public function addMemberFee(MemberFee $memberFee)
    {
        $memberFee->setBilling($this);
        $this->memberFees[] = $memberFee;
        return $this;
    }

    /**
     *
     * @return Segment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     *
     * @param Segment $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
        return $this;
    }

    public function getSepaCreditor()
    {
        return $this->sepaCreditor;
    }

    public function setSepaCreditor($value)
    {
        $this->sepaCreditor = $value;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($value)
    {
        $this->template = $value;
        return $this;
    }

    public function getPayedTotal()
    {
        return $this->payedTotal;
    }

    public function setPayedTotal($value)
    {
        $this->payedTotal = $value;
        return $this;
    }

    /**
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
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
     * @return MemberBilling
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
     * Returns the base string for labels.
     * each line starts with Positionflag
     * <pre>
     * FEE Membership fee from [STARTDATE] to [ENDDATE]
     * ADMISSION Admission fee
     * FEECORRECTION Correction of fee
     * </pre>
     */
    public function getPositionLabels()
    {
        return $this->positionLabels;
    }

    public function getPositionLabelMap()
    {
        $labels = [];
        $lines = $this->parseLines($this->positionLabels);
        foreach ($lines as $line) {
            if (trim($line)) {
                list ($key, $label) = explode(' ', $line, 2);
                $labels[$key] = trim($label);
            }
        }
        return $labels;
    }
    private function parseLines($line)
    {
        $keys = [
            MemberFeePosition::FLAG_ADMISSON,
            MemberFeePosition::FLAG_FEE,
            MemberFeePosition::FLAG_CORRECTION,
        ];
        $posArr = [];
        foreach ($keys as $key) {
            $posArr[$key] = mb_strpos($line, $key.' ');
        }

        $posArr = array_flip($posArr);
        ksort($posArr);
        $labelPositions = [];
        foreach ($posArr as $pos => $key) {
            $labelPositions[] = ['pos' => $pos, 'key' => $key];
        }
        $result = [];
        $isLast = false;
        foreach ($labelPositions as $idx => $posData) {
            $isLast = !isset($labelPositions[$idx+1]);
            $startPos = $posData['pos'];
            $nextStart = $isLast ? mb_strlen($line) : $labelPositions[$idx + 1]['pos'];
            $length = $nextStart - $startPos;
            $result[] = trim(mb_substr($line, $startPos, $length));

        }
        return $result;
    }

    public function setPositionLabels($value)
    {
        $this->positionLabels = $value;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     *
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
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getUniqueMessageIdentification()
     */
    public function getUniqueMessageIdentification()
    {
        // will be generated
        return null;
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getInitiatingPartyName()
     */
    public function getInitiatingPartyName()
    {
        if ($this->getSepaCreditor())
            return $this->getSepaCreditor()->getName();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getPaymentId()
     */
    public function getPaymentId()
    {
        return 'bill-' . ($this->getId());
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getCreditorName()
     */
    public function getCreditorName()
    {
        if ($this->getSepaCreditor())
            return $this->getSepaCreditor()->getName();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getCreditorIban()
     */
    public function getCreditorIban()
    {
        if ($this->getSepaCreditor())
            return $this->getSepaCreditor()->getIban();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getCreditorBic()
     */
    public function getCreditorBic()
    {
        if ($this->getSepaCreditor())
            return $this->getSepaCreditor()->getBic();
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface::getCreditorId()
     */
    public function getCreditorId()
    {
        if ($this->getSepaCreditor())
            return $this->getSepaCreditor()->getCreditorId();
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }
}
