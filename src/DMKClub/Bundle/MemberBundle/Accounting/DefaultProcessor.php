<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;


use DMKClub\Bundle\MemberBundle\Form\Type\DefaultProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use Doctrine\ORM\EntityManager;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeSpanCalculator;
/**
 */
class DefaultProcessor implements ProcessorInterface {
	const NAME = 'default';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

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
	/* (non-PHPdoc)
	 * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
	 */
	public function execute(Member $member, MemberBilling $memberBilling, array $options) {
		//
		$memberFee = new MemberFee();
		$position = new MemberFeePosition();
		$memberFee->addPosition($position);

		// Monate ermitteln
		$startDate = $memberBilling->getStartDate();
		$endDate = $memberBilling->getEndDate();
		$calculator = new TimeSpanCalculator();
		$months = $calculator->calculateTimePeriods($startDate, $endDate);

		$feeFull = (int) $options['fee'];
		$fee = 0;
		// Über jeden Monat iterieren
		/* @var $currentMonth \DateTime */
		$currentMonth = $startDate;
		foreach ($months As $idx => $interval) {
			if($this->isMembershipActive($member, $currentMonth)) {
				$fee += $feeFull;
			}
			// War das Mitglied in dem Monat Mitglied?
			$currentMonth = $currentMonth->add($interval);
		}

		$position->setPriceSingle($fee);
		$position->setPriceTotal($fee);



		return $memberFee;
	}
	/**
	 * Is the member active in given month
	 * @param \DMKClub\Bundle\MemberBundle\Entity\Member $member
	 * @param unknown $currentMonth
	 */
	protected function isMembershipActive($member, $currentMonth) {
		$current = (int)$currentMonth->format('Ym');
		$memberShipStarted = (int)$member->getStartDate()->format('Ym') <= $current;
		$memberShipNotEnded = $member->getEndDate() == NULL || (int)$member->getEndDate()->format('Ym') >= $current;
//print_r(['curr' => $current, 'end' => $member->getEndDate(), 'notEnded' => $memberShipNotEnded ]);
		return $memberShipStarted && $memberShipNotEnded;
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository
	 */
	public function getMemberRepository() {
		return $this->em->getRepository('DMKClubMemberBundle:Member');
	}

}
