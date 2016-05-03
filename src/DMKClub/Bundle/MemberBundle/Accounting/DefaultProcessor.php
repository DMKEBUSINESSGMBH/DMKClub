<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use Doctrine\ORM\EntityManager;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeCalculator;
/**
 */
class DefaultProcessor implements ProcessorInterface {
	const NAME = 'default';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	private $memberBilling;
	private $options;

	/**
	 * @return MemberBilling
	 */
	protected function getMemberBilling() {
	  return $this->memberBilling;
	}

	protected function getOption($key) {
		return $this->options[$key];
	}


	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
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
	public function init(MemberBilling $memberBilling, array $options) {

	}
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
	 */
	public function execute(Member $member) {
		//
		$memberFee = new MemberFee();
		$position = new MemberFeePosition();
		$memberFee->addPosition($position);

		$memberBilling = $this->getMemberBilling();
		// Monate ermitteln
		$startDate = $memberBilling->getStartDate();
		$endDate = $memberBilling->getEndDate();
		$calculator = new TimeCalculator();
		$months = $calculator->calculateTimePeriods($startDate, $endDate);

		$feeFull = (int) $this->getOption('fee');
		$feeReduced = (int) $this->getOption('fee_reduced');
		$fee = 0;
		// Über jeden Monat iterieren
		/* @var $currentMonth \DateTime */
		$currentMonthFirstDay = $startDate;
		$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		foreach ($months As $interval) {
			if($this->isMembershipActive($member, $currentMonthFirstDay)) {
				$periodFee = $feeFull;
				if($this->isMembershipReduced($member, $currentMonthLastDay)) {
					$periodFee = $feeReduced;
				}
				$fee += $periodFee;
			}
			// War das Mitglied in dem Monat Mitglied?
			$currentMonthFirstDay = $currentMonthFirstDay->add($interval);
			$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		}

		$position->setPriceSingle($fee);
		$position->setPriceTotal($fee);



		return $memberFee;
	}
	/**
	 * Is the member active in given month
	 * @param \DMKClub\Bundle\MemberBundle\Entity\Member $member
	 * @param \DateTime $currentMonth
	 */
	protected function isMembershipActive($member, $currentMonth) {
		$current = (int)$currentMonth->format('Ym');
		$memberShipStarted = (int)$member->getStartDate()->format('Ym') <= $current;
		$memberShipNotEnded = $member->getEndDate() == NULL || (int)$member->getEndDate()->format('Ym') >= $current;
		return $memberShipStarted && $memberShipNotEnded;
	}
	/**
	 * Is member not full aged in current month
	 * @param Member $member
	 * @param \DateTime $currentMonth
	 */
	protected function isMembershipReduced($member, $currentMonth) {
		// currentMonth steht immer auf dem 1. des Monats. Wer in dem
		// Monat 18 wird, ist also am 1. noch 17 Jahre alt.
		$age = $member->getContact()->getBirthday()->diff($currentMonth)->y;
		return $age < 18;
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
	 */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}

}
