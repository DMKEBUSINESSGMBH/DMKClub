<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Form\Type\SimpleProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
/**
 */
abstract class AbstractProcessor implements ProcessorInterface {
	protected $memberBilling;
	protected $options;


	public function init(MemberBilling $memberBilling, array $options) {
		$this->memberBilling = $memberBilling;
		$this->options = $options;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel()
	{
		return 'dmkclub.member.accounting.processor.' . $this->getName();
	}

	/**
	 * @return MemberBilling
	 */
	protected function getMemberBilling() {
	  return $this->memberBilling;
	}

	protected function getOption($key) {
		return $this->options[$key];
	}


	public function formatSettings(array $options) {
		return $options;
	}

}
