<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\SimpleProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
/**
 */
class SimpleProcessor extends AbstractProcessor {
	const NAME = 'simple';

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return self::NAME;
	}

	/**
	 * (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::getFields()
	 */
	public function getFields() {
		return [
				'fee',
		];
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
