<?php
namespace DMKClub\Bundle\MemberBundle\Accounting;

use DMKClub\Bundle\MemberBundle\Form\Type\SimpleProcessorSettingsType;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use Psr\Log\LoggerInterface;
use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeCalculator;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

/**
 */
class SimpleProcessor extends AbstractProcessor
{

    const NAME = 'simple';

    const OPTION_FEE = 'fee';

    /* @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::getFields()
     */
    public function getFields()
    {
        return [
            'fee'
        ];
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getSettingsFormType()
    {
        return SimpleProcessorSettingsType::class;
    }

    /*
     * (non-PHPdoc)
     * @see \DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface::execute()
     */
    public function execute(Member $member)
    {
        $this->assertMember($member);

        $memberBilling = $this->getMemberBilling();
        $labelMap = $memberBilling->getPositionLabelMap();
        // Monate ermitteln
        $startDate = $memberBilling->getStartDate();
        $endDate = $memberBilling->getEndDate();
        $calculator = new TimeCalculator();
        $months = $calculator->calculateTimePeriods($startDate, $endDate);

        $feeFull = (int) $this->getOption(self::OPTION_FEE);

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
                $fee += $periodFee;
            }
            $currentMonthFirstDay = $currentMonthFirstDay->add($interval);
        }
        // Enddatum auf den Monatsletzten setzen
        if($lastMonth2Pay) {
            $lastMonth2Pay->add($interval);
            $lastMonth2Pay->sub(new \DateInterval('P1D'));
        }

        $this->logger->info("Fee: " . $fee . " from " . $startDate->format('Y-m-d') . ' to '.$endDate->format('Y-m-d'));

        $descriptionFeePosition = isset($labelMap[MemberFeePosition::FLAG_FEE]) ?
            $labelMap[MemberFeePosition::FLAG_FEE] : MemberFeePosition::FLAG_FEE;

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

        $memberFee->updatePriceTotal();

        return $memberFee;

    }
}
