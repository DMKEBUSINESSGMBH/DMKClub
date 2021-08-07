<?php
namespace DMKClub\Bundle\MemberBundle\Accounting;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

/**
 */
abstract class AbstractProcessor implements ProcessorInterface
{

    protected $memberBilling;

    protected $options;

    public function init(MemberBilling $memberBilling, array $options)
    {
        $this->memberBilling = $memberBilling;
        $this->options = $options;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'dmkclub.member.accounting.processor.' . $this->getName();
    }

    /**
     *
     * @return MemberBilling
     */
    protected function getMemberBilling()
    {
        return $this->memberBilling;
    }

    protected function getOption($key)
    {
        return $this->options[$key];
    }

    public function formatSettings(array $options)
    {
        return $options;
    }

    protected function assertMember(Member $member)
    {
        // Alle Pflichtdaten prüfen
        if (! $member->getStartDate()) {
            throw new AccountingException('Member with id [' . $member->getId() . '] has no start date');
        }
    }

    /**
     * Is the member active in given month
     *
     * @param \DMKClub\Bundle\MemberBundle\Entity\Member $member
     * @param \DateTime $currentMonth
     */
    protected function isMembershipActive($member, $currentMonth)
    {
        $current = (int) $currentMonth->format('Ym');
        $memberShipStarted = (int) $member->getStartDate()->format('Ym') <= $current;
        $memberShipNotEnded = $member->getEndDate() == NULL || (int) $member->getEndDate()->format('Ym') >= $current;
        return $memberShipStarted && $memberShipNotEnded;
    }

    protected function newDate($formatString)
    {
        return new \DateTime($formatString, new \DateTimeZone('UTC'));
    }

    protected function prepareDescriptionFeePosition($descriptionFeePosition, $labelStartDate, $labelEndDate)
    {
        $dateFormat = 'd.m.Y';
        $descriptionFeePosition = str_replace('[STARTDATE]', $labelStartDate->format($dateFormat), $descriptionFeePosition);
        $descriptionFeePosition = str_replace('[ENDDATE]', $labelEndDate->format($dateFormat), $descriptionFeePosition);
        return $descriptionFeePosition;
    }

    protected function createMemberFee(Member $member, MemberBilling $memberBilling,
        $labelStartDate, $labelEndDate): MemberFee
    {

        $memberFee = new MemberFee();
        $memberFee->setStartDate($labelStartDate);
        $memberFee->setEndDate($labelEndDate);
        if ($bankAccount = $member->getBankAccount()) {
            $memberFee->setDirectDebitMandateId($bankAccount->getDirectDebitMandateId());
        }
        // Default Verwendungszweck
        $memberFee->setRemittanceInformation(
            sprintf('%s-%s', $memberBilling->getSign(), $member->getMemberCode())
        );
        return $memberFee;
    }
}
