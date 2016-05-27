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
use BeSimple\SoapCommon\Type\KeyValue\DateTime;
/**
 */
class DefaultProcessor extends AbstractProcessor {
	const NAME = 'default';
	const OPTION_FEE = 'fee';
	const OPTION_FEE_DISCOUNT = 'fee_discount';
	const OPTION_FEE_CHILD = 'fee_child';
	const OPTION_AGE_CHILD = 'age_child';

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
	 * (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::getFields()
	 */
	public function getFields() {
		return [
				self::OPTION_FEE,
				self::OPTION_FEE_DISCOUNT,
				self::OPTION_FEE_CHILD,
				self::OPTION_AGE_CHILD,
		];
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
		$feeDiscount = (int) $this->getOption(self::OPTION_FEE_DISCOUNT);
		$feeChild = (int) $this->getOption(self::OPTION_FEE_CHILD);
		$ageChild = (int) $this->getOption(self::OPTION_AGE_CHILD);

		$fee = 0;
		// Über jeden Monat iterieren
		/* @var $currentMonth \DateTime */
		$currentMonthFirstDay = new \DateTime($startDate->format('Y-m-d'));
		$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		foreach ($months As $interval) {
			/* @var $interval \DateInterval */
			if($this->isMembershipActive($member, $currentMonthFirstDay)) {
				$periodFee = $feeFull;
				if($this->isMembershipChild($member, $currentMonthFirstDay, $ageChild)) {
					$periodFee = $feeChild;
				}
				elseif($this->isMembershipDiscount($member, $currentMonthFirstDay)) {
					$periodFee = $feeDiscount;
				}
				$fee += $periodFee;
			}
			// War das Mitglied in dem Monat Mitglied?
			$currentMonthFirstDay = $currentMonthFirstDay->add($interval);
			$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		}
		$this->writeLog("Fee: " . $fee . " from " . $startDate->format('Y-m-d') . ' to '.$endDate->format('Y-m-d'));

		$descriptionFeePosition = 'Beitrag vom [STARTDATE] bis [ENDDATE]';
		$dateFormat = 'd.m.Y';
		$descriptionFeePosition = str_replace('[STARTDATE]', $startDate->format($dateFormat), $descriptionFeePosition);
		$descriptionFeePosition = str_replace('[ENDDATE]', $endDate->format($dateFormat), $descriptionFeePosition);


		$position->setDescription($descriptionFeePosition);
		$position->setQuantity(1);
		$position->setPriceSingle($fee);
		$position->setPriceTotal($fee);
		$position->setFlag('FEE');
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
	protected function isMembershipChild($member, $currentMonth, $ageChild) {
		// currentMonth steht immer auf dem 1. des Monats. Wer in dem
		// Monat 18 wird, ist also am 1. noch 17 Jahre alt.
		// Der volle Beitrag gilt erst im Folgemonat
		if(!$member->getContact())
			return FALSE;
		$birthday = $member->getContact()->getBirthday();
		if(!$birthday)
			return FALSE;
		$age = $birthday->diff($currentMonth)->y;
		//print_r([$currentMonth->format('Y-m-d') => ($age < $ageChild), 'age' => $age ]);
		return $age < $ageChild;
	}
	/**
	 * Is member discount active in current month
	 * @param Member $member
	 * @param \DateTime $currentMonthLastDay
	 */
	protected function isMembershipDiscount(Member $member, $currentMonthLastDay) {
		foreach($member->getMemberFeeDiscounts() As $feeDiscount) {
			// Ist das Datum in $month innerhalb der Discount-Zeit?
			if($feeDiscount->contains($currentMonthLastDay)) {
				return true;
			}
		}
		return false;
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
			if($key == self::OPTION_FEE || $key == self::OPTION_FEE_CHILD || $key == self::OPTION_FEE_DISCOUNT)
				$value = number_format($value/100,2);
			$ret[$key] = $value;
		}
		return $ret;
	}
}
