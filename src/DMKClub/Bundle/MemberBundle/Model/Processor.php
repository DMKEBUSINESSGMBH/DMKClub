<?php

namespace DMKClub\Bundle\MemberBundle\Model;

class Processor {
	private $key;
	private $label;

	public function getKey() {
	  return $this->key;
	}

	public function setKey($value) {
	  $this->key = $value;
	}

	public function getLabel() {
	  return $this->label;
	}

	public function setLabel($value) {
	  $this->label = $value;
	}

}
