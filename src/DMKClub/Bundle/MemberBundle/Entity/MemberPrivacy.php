<?php
namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

use DMKClub\Bundle\MemberBundle\Model\ExtendMemberPrivacy;

/**
 * Class MemberPrivacy
 *
 * @ORM\Entity
 * @ORM\Table(name="dmkclub_member_privacy")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *   defaultValues={
 *     "entity"={
 *       "icon"="fa-money"
 *     },
 *     "dataaudit"={
 *       "auditable"=true
 *     }
 *   }
 * )
 */
class MemberPrivacy extends ExtendMemberPrivacy
{

    /**
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *   defaultValues={
     *     "importexport"={
     *       "order"=10
     *     }
     *   }
     * )
     */
    protected $id;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="sign_date", type="date", nullable=true)
     * @ConfigField(
     * defaultValues={
     *   "dataaudit"={"auditable"=true},
     *   "importexport"={
     *     "order"=75
     *   }
     * }
     * )
     */
    protected $signDate;

    /**
     * @ORM\OneToOne(targetEntity="\DMKClub\Bundle\MemberBundle\Entity\Member", inversedBy="privacy")
     * @ORM\JoinColumn(name="member", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $member;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="phone_allowed", options={"default" : false})
     * @ConfigField(
     * 	   defaultValues={
     *         "dataaudit"={"auditable"=true},
     *         "importexport"={
     *             "order"=40
     *         }
     *     }
     * )
     */
    protected $phoneAllowed = false;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="email_allowed", options={"default" : false})
     * @ConfigField(
     * 	   defaultValues={
     *         "dataaudit"={"auditable"=true},
     *         "importexport"={
     *             "order"=40
     *         }
     *     }
     * )
     */
    protected $emailAllowed = false;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="postal_allowed", options={"default" : false})
     * @ConfigField(
     * 	   defaultValues={
     *         "dataaudit"={"auditable"=true},
     *         "importexport"={
     *             "order"=40
     *         }
     *     }
     * )
     */
    protected $postalAllowed = false;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="merchandising_allowed", options={"default" : false})
     * @ConfigField(
     * 	   defaultValues={
     *         "dataaudit"={"auditable"=true},
     *         "importexport"={
     *             "order"=40
     *         }
     *     }
     * )
     */
    protected $merchandisingAllowed = false;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="sharing_allowed", options={"default" : false})
     * @ConfigField(
     * 	   defaultValues={
     *         "dataaudit"={"auditable"=true},
     *         "importexport"={
     *             "order"=40
     *         }
     *     }
     * )
     */
    protected $sharingAllowed = false;

    /**
     *
     * @var \DateTime $createdAt
     * @ORM\Column(type="datetime", name="created_at")
     * @ConfigField(
     *   defaultValues={
     *     "entity"={"label"="oro.ui.created_at"},
     *     "importexport"={"excluded"=true}
     *   }
     * )
     */
    protected $createdAt;

    /**
     *
     * @var \DateTime $updatedAt
     * @ORM\Column(type="datetime", name="updated_at")
     * @ConfigField(
     *   defaultValues={
     *     "entity"={"label"="oro.ui.updated_at"},
     *     "importexport"={"excluded"=true}
     *   }
     * )
     */
    protected $updatedAt;

    /**
     *
     * {@inheritdoc}
     *
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
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->member;
    }

    public function setMember($member)
    {
        $this->member = $member;
        return $this;
    }

    /**
     * Set signDate
     *
     * @param \DateTime $signDate
     * @return Member
     */
    public function setSignDate($startDate)
    {
        $this->signDate = $startDate;

        return $this;
    }

    /**
     * Get signDate
     *
     * @return \DateTime
     */
    public function getSignDate()
    {
        return $this->signDate;
    }

    /**
     * @return boolean
     */
    public function isPhoneAllowed()
    {
        return $this->phoneAllowed;
    }

    /**
     * @param boolean $phoneAllowed
     */
    public function setPhoneAllowed($phoneAllowed)
    {
        $this->phoneAllowed = $phoneAllowed;
    }

    /**
     * @return boolean
     */
    public function isEmailAllowed()
    {
        return $this->emailAllowed;
    }

    /**
     * @param boolean $emailAllowed
     */
    public function setEmailAllowed($emailAllowed)
    {
        $this->emailAllowed = $emailAllowed;
    }

    /**
     * @return boolean
     */
    public function isPostalAllowed()
    {
        return $this->postalAllowed;
    }

    /**
     * @param boolean $postalAllowed
     */
    public function setPostalAllowed($postalAllowed)
    {
        $this->postalAllowed = $postalAllowed;
    }

    /**
     * @return boolean
     */
    public function isMerchandisingAllowed()
    {
        return $this->merchandisingAllowed;
    }

    /**
     * @param boolean $merchandisingAllowed
     */
    public function setMerchandisingAllowed($merchandisingAllowed)
    {
        $this->merchandisingAllowed = $merchandisingAllowed;
    }

    /**
     * @return boolean
     */
    public function isSharingAllowed()
    {
        return $this->sharingAllowed;
    }

    /**
     * @param boolean $sharingAllowed
     */
    public function setSharingAllowed($sharingAllowed)
    {
        $this->sharingAllowed = $sharingAllowed;
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
}
