<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
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
	 * @param \DMKClub\Bundle\MemberBundle\Entity\Member $member
	 * @return \DMKClub\Bundle\MemberBundle\Entity\MemberFee
	 */
	public function execute(Member $member);
	/**
	 *
	 * @param MemberBilling $entity
	 * @param array $options
	 */
	public function init(MemberBilling $entity, array $options);

}
