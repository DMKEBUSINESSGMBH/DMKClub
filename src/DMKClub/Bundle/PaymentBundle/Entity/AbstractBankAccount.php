<?php
namespace DMKClub\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

/**
 * Class BankAccount
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class AbstractBankAccount
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
     * @ORM\Column(name="account_owner", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
     *      "importexport"={
     *        "order"=10
     *      }
     *   }
     * )
     */
    protected $accountOwner;

    /**
     *
     * @var string
     * @ORM\Column(name="iban", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
     *      "importexport"={
     *        "order"=20
     *      }
     *    }
     * )
     */
    protected $iban;

    /**
     *
     * @var string
     * @ORM\Column(name="bic", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
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
     * @ORM\Column(name="bank_name", type="string", length=255, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
     *      "importexport"={
     *        "order"=40
     *      }
     *    }
     * )
     */
    protected $bankName;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="direct_debit_valid_from", type="date", nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
     *      "importexport"={
     *        "order"=50
     *      }
     *    }
     * )
     */
    protected $directDebitValidFrom;

    /**
     * Default SEPA direct debit mandate id
     *
     * @var string
     * @ORM\Column(name="direct_debit_mandate_id", type="string", length=50, nullable=true)
     * @ConfigField(
     *    defaultValues={
     *      "dataaudit"={
     *        "auditable"=true
     *      },
     *      "importexport"={
     *        "order"=60
     *      }
     *    }
     * )
     */
    protected $directDebitMandateId;

    /**
     *
     * @var \DateTime
     * $created @ORM\Column(type="datetime")
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
     * @return AbstractBankAccount
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    public function setAccountOwner($value)
    {
        $this->accountOwner = $value;
        return $this;
    }

    public function getBankName()
    {
        return $this->bankName;
    }

    public function setBankName($value)
    {
        $this->bankName = $value;
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

    public function getDirectDebitValidFrom()
    {
        return $this->directDebitValidFrom;
    }

    public function setDirectDebitValidFrom($value)
    {
        $this->directDebitValidFrom = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectDebitMandateId()
    {
        return $this->directDebitMandateId;
    }

    /**
     * @param string $directDebitMandateId
     */
    public function setDirectDebitMandateId($directDebitMandateId)
    {
        $this->directDebitMandateId = $directDebitMandateId;
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
     * @return AbstractBankAccount
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
     * @return AbstractBankAccount
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
            $this->getAccountOwner(),
            ',',
            $this->getBankName(),
            $this->getIban(),
            $this->getBic()
        );
        $str = implode(' ', $data);
        $check = trim(str_replace(',', '', $str));
        return empty($check) ? '' : $str;
    }
}
