<?php

namespace DMKClub\Bundle\MemberBundle\Model;

class ProcessorSettings {
	private $name;

	public function getName() {
	  return $this->name;
	}

	public function setName($value) {
	  $this->name = $value;
	}

}
