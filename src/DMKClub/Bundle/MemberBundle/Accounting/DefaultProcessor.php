<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
/**
 */
class DefaultProcessor implements ProcessorInterface {
	const NAME = 'default';

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return self::NAME;
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
		return DefaultProcessorSettingsType::NAME;
	}
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
	 */
	public function execute(MemberBilling $entity, array $options) {
		return ['success' => get_class($this)];
	}

}
