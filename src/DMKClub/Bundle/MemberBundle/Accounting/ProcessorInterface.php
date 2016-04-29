<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
interface ProcessorInterface {
	/**
	 * Get processor name.
	 *
	 * @return string
	 */
	public function getName();
	/**
	 * Get label used for processor selection.
	 *
	 * @return string
	 */
	public function getLabel();

	/**
	 * Returns form type name needed to setup transport.
	 *
	 * @return string
	 */
	public function getSettingsFormType();

	/**
	 * Start processing
	 * @param MemberBilling $entity
	 * @param array $options
	 */
	public function execute(MemberBilling $entity, array $options);

}
