<?php

namespace DMKClub\Bundle\PaymentBundle\Sepa;


interface SepaDirectDebitAwareInterface {

	/**
	 * Whether or not a direct debit should be created
	 * @return bool
	 */
	public function isSepaDirectDebitPossible();

	/**
	 * @return SepaPaymentAwareInterface
	 */
	public function getPaymentAware();

	/**
	 * Get Amount for SEPA-Transfer in cent
	 * @return int
	 */
	public function getSepaAmount();

	/**
	 * @return string
	 */
	public function getRemittanceInformation();

	/**
	 *
	 * @return string
	 */
	public function getDebtorName();

	/**
	 * @return string
	 */
	public function getDebtorBic();

	/**
	 * @return string
	 */
	public function getDebtorIban();
	/**
	 * @return \DateTime
	 */
	public function getDebtorMandateSignDate();

}
