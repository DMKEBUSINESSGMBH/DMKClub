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
use Psr\Log\LoggerInterface;
/**
 */
class DefaultProcessor extends AbstractProcessor {
	const NAME = 'default';
	const OPTION_FEE = 'fee';
	const OPTION_FEE_REDUCED = 'fee_reduced';
	const OPTION_AGE_REDUCED = 'age_reduced';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	/* @var \Psr\Log\LoggerInterface */
	private $logger;



	public function __construct(LoggerInterface $logger, EntityManager $em) {
		$this->em = $em;
		$this->logger = $logger;
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
	public function getSettingsFormType()
	{
		return DefaultProcessorSettingsType::NAME;
	}
	public function init(MemberBilling $memberBilling, array $options) {
		$this->memberBilling = $memberBilling;
		$this->options = $options;
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

		$feeFull = (int) $this->getOption(self::OPTION_FEE);
		$feeReduced = (int) $this->getOption(self::OPTION_FEE_REDUCED);
		$ageReduced = (int) $this->getOption(self::OPTION_AGE_REDUCED);

		$fee = 0;
		// Über jeden Monat iterieren
		/* @var $currentMonth \DateTime */
		$currentMonthFirstDay = new \DateTime($startDate->format('Y-m-d'));
		$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		foreach ($months As $interval) {
			if($this->isMembershipActive($member, $currentMonthFirstDay)) {
				$periodFee = $feeFull;
				if($this->isMembershipReduced($member, $currentMonthLastDay, $ageReduced)) {
					$periodFee = $feeReduced;
				}
				$fee += $periodFee;
			}
			// War das Mitglied in dem Monat Mitglied?
			$currentMonthFirstDay = $currentMonthFirstDay->add($interval);
			$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		}
		$this->writeLog("Fee: " . $fee . " from " . $startDate->format('Y-m-d') . ' to '.$endDate->format('Y-m-d'));

		$position->setPriceSingle($fee);
		$position->setPriceTotal($fee);
		$memberFee->setPriceTotal($fee);



		return $memberFee;
	}
	private function writeLog($message) {
		$this->logger->info($message);
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
	protected function isMembershipReduced($member, $currentMonth, $ageReduced) {
		// currentMonth steht immer auf dem 1. des Monats. Wer in dem
		// Monat 18 wird, ist also am 1. noch 17 Jahre alt.
		$age = $member->getContact()->getBirthday()->diff($currentMonth)->y;
		return $age < $ageReduced;
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
	 */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}

	public function formatSettings(array $options) {
		$ret = array();
		foreach ($options As $key => $value) {
			if($key == self::OPTION_FEE || $key == self::OPTION_FEE_REDUCED)
				$value = number_format($value/100,2);
			$ret[$key] = $value;
		}
		return $ret;
	}
}
