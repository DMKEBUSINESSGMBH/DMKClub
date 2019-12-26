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
	const OPTION_FEE_ADMISSION = 'fee_admission';
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
				self::OPTION_FEE_ADMISSION,
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
		return DefaultProcessorSettingsType::class;
	}
	protected function assertMember(Member $member) {
		// Alle Pflichtdaten prüfen
		if(!$member->getStartDate())
			throw new AccountingException('Member with id [' . $member->getId() . '] has no start date');
	}
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
	 */
	public function execute(Member $member) {
		$this->assertMember($member);

		$memberBilling = $this->getMemberBilling();
		$labelMap = $memberBilling->getPositionLabelMap();
		// Monate ermitteln
		$startDate = $memberBilling->getStartDate();

		$endDate = $memberBilling->getEndDate();
		$calculator = new TimeCalculator();
		$months = $calculator->calculateTimePeriods($startDate, $endDate);

		$feeFull = (int) $this->getOption(self::OPTION_FEE);
		$feeDiscount = (int) $this->getOption(self::OPTION_FEE_DISCOUNT);
		$feeAdmission = (int) $this->getOption(self::OPTION_FEE_ADMISSION);
		$feeChild = (int) $this->getOption(self::OPTION_FEE_CHILD);
		$ageChild = (int) $this->getOption(self::OPTION_AGE_CHILD);

		$fee = 0;
		// Über jeden Monat iterieren, den erste und den letzten Monat merken
		$firstMonth2Pay = null;
		$lastMonth2Pay = null;
		/* @var $currentMonth \DateTime */
		$currentMonthFirstDay = $this->newDate($startDate->format('Y-m-d'));
//		$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		foreach ($months As $interval) {

		    // War das Mitglied in dem Monat Mitglied?
		    /* @var $interval \DateInterval */
			if($this->isMembershipActive($member, $currentMonthFirstDay)) {
			    if($firstMonth2Pay === null) {
			        $firstMonth2Pay = clone $currentMonthFirstDay;
			    }
		        $lastMonth2Pay = clone $currentMonthFirstDay;
			    $periodFee = $feeFull;
				if($this->isMembershipChild($member, $currentMonthFirstDay, $ageChild)) {
					$periodFee = $feeChild;
				}
				elseif($this->isMembershipDiscount($member, $currentMonthFirstDay)) {
					$periodFee = $feeDiscount;
				}
				$fee += $periodFee;
			}
			$currentMonthFirstDay = $currentMonthFirstDay->add($interval);
//			$currentMonthLastDay = $calculator->getLastDayInMonth($currentMonthFirstDay);
		}
		// Enddatum auf den Monatsletzten setzen
		if($lastMonth2Pay) {
		    $lastMonth2Pay->add($interval);
		    $lastMonth2Pay->sub(new \DateInterval('P1D'));
		}

		$this->writeLog("Fee: " . $fee . " from " . $startDate->format('Y-m-d') . ' to '.$endDate->format('Y-m-d'));

		// $descriptionFeePosition = 'Beitrag vom [STARTDATE] bis [ENDDATE]';
		$descriptionFeePosition = isset($labelMap[MemberFeePosition::FLAG_FEE]) ?
				$labelMap[MemberFeePosition::FLAG_FEE] : 'MemberFeePosition::FLAG_FEE';

		$dateFormat = 'd.m.Y';
		// Bei unterjährigem Ein- und Austritt das passende Datum verwenden
		$labelStartDate = $firstMonth2Pay ? $firstMonth2Pay : $startDate;
		$labelEndDate = $lastMonth2Pay ? $lastMonth2Pay : $endDate;
		$descriptionFeePosition = str_replace('[STARTDATE]', $labelStartDate->format($dateFormat), $descriptionFeePosition);
		$descriptionFeePosition = str_replace('[ENDDATE]', $labelEndDate->format($dateFormat), $descriptionFeePosition);

		$memberFee = new MemberFee();
		$memberFee->setStartDate($labelStartDate);
		$memberFee->setEndDate($labelEndDate);

		$position = new MemberFeePosition();
		$memberFee->addPosition($position);

		$position->setDescription($descriptionFeePosition);
		$position->setQuantity(1);
		$position->setPriceSingle($fee);
		$position->setPriceTotal($fee);
		$position->setFlag(MemberFeePosition::FLAG_FEE);

		// Aufnahmegebühr
		if($feeAdmission > 0 && $this->isNewMembership($member, $startDate, $endDate)) {
			// Ist das Mitglied im Berechnungszeitraum neu eingetreten
			$label = isset($labelMap[MemberFeePosition::FLAG_ADMISSON]) ?
					$labelMap[MemberFeePosition::FLAG_ADMISSON] : 'MemberFeePosition::FLAG_ADMISSON';
			$position = new MemberFeePosition();
			$memberFee->addPosition($position);
			$position->setDescription($label);
			$position->setQuantity(1);
			$position->setPriceSingle($feeAdmission);
			$position->setPriceTotal($feeAdmission);
			$position->setFlag(MemberFeePosition::FLAG_ADMISSON);
		}

		$memberFee->updatePriceTotal();

		return $memberFee;
	}
	protected function newDate($formatString)
	{
		return new \DateTime($formatString, new \DateTimeZone('UTC'));
	}
	private function isNewMembership($member, $startDate, $endDate) {
		return $member->getStartDate() >= $startDate && $member->getStartDate() <= $endDate;
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
			/* @var $feeDiscount \DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount */
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
			if($key == self::OPTION_FEE || $key == self::OPTION_FEE_CHILD
					|| $key == self::OPTION_FEE_DISCOUNT || $key == self::OPTION_FEE_ADMISSION)
				$value = number_format($value/100,2);
			$ret[$key] = $value;
		}
		return $ret;
	}
}
