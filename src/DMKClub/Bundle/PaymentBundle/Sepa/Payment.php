<?php

namespace DMKClub\Bundle\PaymentBundle\Sepa;

use Digitick\Sepa\PaymentInformation;

class Payment {
	private $id;
	private $creditorName;
	private $creditorAccountIBAN;
	private $creditorAgentBIC;
	private $seqType = PaymentInformation::S_ONEOFF;
	private $creditorId;

	public function getId() {
		return $this->id;
	}

	public function setId($value) {
		$this->id = $value;
		return $this;
	}

	public function getCreditorName() {
	  return $this->creditorName;
	}

	public function setCreditorName($value) {
	  $this->creditorName = $value;
		return $this;
	}

	public function getCreditorAccountIBAN() {
	  return $this->creditorAccountIBAN;
	}

	public function setCreditorAccountIBAN($value) {
	  $this->creditorAccountIBAN = $value;
		return $this;
	}

	public function getCreditorAgentBIC() {
	  return $this->creditorAgentBIC;
	}

	public function setCreditorAgentBIC($value) {
	  $this->creditorAgentBIC = $value;
		return $this;
	}

	public function getSeqType() {
	  return $this->seqType;
	}

	public function setSeqType($value) {
	  $this->seqType = $value;
		return $this;
	}

	public function getCreditorId() {
	  return $this->creditorId;
	}

	public function setCreditorId($value) {
	  $this->creditorId = $value;
		return $this;
	}

}
