<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Form\Type\SimpleProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
/**
 */
class SimpleProcessor implements ProcessorInterface {
	const NAME = 'simple';
	private $memberBilling;
	private $options;

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return self::NAME;
	}

	public function init(MemberBilling $memberBilling, array $options) {
		$this->memberBilling = $memberBilling;
		$this->options = $options;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLabel()
	{
		return 'dmkclub.member.accounting.processor.' . self::NAME;
	}
	/**
	 * {@inheritdoc}
	 */
	public function getSettingsFormType()
	{
		return SimpleProcessorSettingsType::NAME;
	}
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
	 */
	public function execute(Member $member) {
		return ['success' => get_class($this)];
	}
}
