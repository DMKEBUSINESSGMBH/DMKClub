<?php
namespace DMKClub\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use DMKClub\Bundle\PaymentBundle\Model\ExtendSepaCreditor;

/**
 * Class SepaCreditor
 *
 * @package DMKClub\Bundle\DMKClubPaymentBundle\Entity
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\PaymentBundle\Entity\Repository\SepaCreditorRepository")
 * @ORM\Table(name="dmkclub_sepacreditor")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-suitcase"
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
 *              "grid_name"="dmkclub-sepacreditor-grid",
 *              "form_type"="dmkclub_sepacreditor_select"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
class SepaCreditor extends ExtendSepaCreditor
{

    /**
     *
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ConfigField(
     *    defaultValues={
     *      "importexport"={
     *        "excluded"=true
     *      }
     *    }
     * )
     */
    protected $id;

    /**
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "importexport"={
     *        "order"=10
     *      }
     *    }
     * )
     */
    protected $name;

    /**
     *
     * @var string @ORM\Column(name="iban", type="string", length=255, nullable=true)
     *      @ConfigField(
     *      defaultValues={
     *      "importexport"={
     *      "order"=20
     *      }
     *      }
     *      )
     */
    protected $iban;

    /**
     *
     * @var string
     * @ORM\Column(name="bic", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "importexport"={
     *        "order"=30
     *      }
     *    }
     * )
     */
    protected $bic;

    /**
     *
     * @var string
     * @ORM\Column(name="creditor_id", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "importexport"={
     *        "order"=40
     *      }
     *    }
     * )
     */
    protected $creditorId;

    /**
     *
     * @var \DateTime $created
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *    defaultValues={
     *      "entity"={
     *        "label"="oro.ui.created_at"
     *      },
     *      "importexport"={
     *        "excluded"=true
     *      }
     *    }
     * )
     */
    protected $created;

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
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     * @ConfigField(
     *    defaultValues={
     *      "importexport"={
     *        "full"=false,
     *        "order"=460
     *      }
     *    }
     * )
     */
    protected $organization;

    /**
     *
     * @var \DateTime $updated
     * @ORM\Column(type="datetime")
     * @ConfigField(
     *    defaultValues={
     *      "entity"={
     *        "label"="oro.ui.updated_at"
     *      },
     *      "importexport"={
     *        "excluded"=true
     *      }
     *    }
     * )
     */
    protected $updated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     * @return SepaCreditor
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    public function getCreditorId()
    {
        return $this->creditorId;
    }

    public function setCreditorId($value)
    {
        $this->creditorId = $value;
        return $this;
    }

    public function getBic()
    {
        return $this->bic;
    }

    public function setBic($value)
    {
        $this->bic = $value;
        return $this;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function setIban($value)
    {
        $this->iban = $value;
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
     * @return SepaCreditor
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
     * Get address created date/time
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set address created date/time
     *
     * @param \DateTime $created
     * @return SepaCreditor
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get address last update date/time
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set address updated date/time
     *
     * @param \DateTime $updated
     * @return SepaCreditor
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $this->created = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        $data = array(
            $this->getName(),
            ',',
            $this->getCreditorId(),
            $this->getIban(),
            $this->getBic()
        );
        $str = implode(' ', $data);
        $check = trim(str_replace(',', '', $str));
        return empty($check) ? '' : $str;
    }
}
