<?php

namespace DMKClub\Bundle\PaymentBundle\Sepa;

use Digitick\Sepa\PaymentInformation;

class Transaction {
	private $debtorIban;
	private $debtorBic;
	private $debtorName;
	private $debtorMandateSignDate;
	private $debtorMandate;
	private $payment;
	private $amount;
	private $RemittanceInformation;

	public function getRemittanceInformation() {
	  return $this->RemittanceInformation;
	}

	public function setRemittanceInformation($value) {
	  $this->RemittanceInformation = $value;
	  return $this;
	}

	public function getAmount() {
	  return $this->amount;
	}

	public function setAmount($value) {
	  $this->amount = $value;
	  return $this;
	}

	public function getDebtorIban() {
	  return $this->debtorIban;
	}

	public function setDebtorIban($value) {
	  $this->debtorIban = $value;
	  return $this;
	}

	public function getDebtorBic() {
	  return $this->debtorBic;
	}

	public function setDebtorBic($value) {
	  $this->debtorBic = $value;
	  return $this;
	}

	public function getDebtorName() {
	  return $this->debtorName;
	}

	public function setDebtorName($value) {
	  $this->debtorName = $value;
	  return $this;
	}

	public function getDebtorMandate() {
	  return $this->debtorMandate;
	}

	public function setDebtorMandate($value) {
	  $this->debtorMandate = $value;
	  return $this;
	}

	/**
	 * @return string  '13.10.2016'
	 */
	public function getDebtorMandateSignDate() {
	  return $this->debtorMandateSignDate;
	}

	public function setDebtorMandateSignDate($value) {
	  $this->debtorMandateSignDate = $value;
	  return $this;
	}

	public function getPayment() {
	  return $this->payment;
	}

	public function setPayment(Payment $value) {
	  $this->payment = $value;
	  return $this;
	}

}
