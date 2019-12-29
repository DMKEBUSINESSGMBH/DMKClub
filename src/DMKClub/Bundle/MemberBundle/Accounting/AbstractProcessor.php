<?php
namespace DMKClub\Bundle\MemberBundle\Accounting;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Member;

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
}
