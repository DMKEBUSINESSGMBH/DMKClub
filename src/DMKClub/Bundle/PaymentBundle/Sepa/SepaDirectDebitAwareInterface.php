<?php
namespace DMKClub\Bundle\PaymentBundle\Sepa;

interface SepaDirectDebitAwareInterface
{

    /**
     * Whether or not a direct debit should be created
     *
     * @return bool
     */
    public function isSepaDirectDebitPossible();

    /**
     *
     * @return SepaPaymentAwareInterface
     */
    public function getPaymentAware();

    /**
     * Get Amount for SEPA-Transfer in cent
     *
     * @return int
     */
    public function getSepaAmount();

    /**
     * Value of RmtInf > Ustrd
     * SEPA Verwendungszweck
     *
     * @return string
     */
    public function getRemittanceInformation();

    /**
     *
     * @return string
     */
    public function getDebtorName();

    /**
     *
     * @return string
     */
    public function getDebtorBic();

    /**
     *
     * @return string
     */
    public function getDebtorIban();

    /**
     * Value of MndtRltdInf > DtOfSgntr
     * @return \DateTime
     */
    public function getDebtorMandateSignDate();

    /**
     * Value of MndtRltdInf > MndtId
     * @return string
     */
    public function getDebtorMandate();
}
