<?php
namespace DMKClub\Bundle\MemberBundle\Async\Accounting;

use Psr\Log\LoggerInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use Doctrine\ORM\EntityManager;
use Oro\Component\MessageQueue\Util\JSON;
use Oro\Component\MessageQueue\Job\JobRunner;

/**
 * Die Klasse erzeugt die MemberFees
 *
 * @author "René Nitzsche"
 */
class FeeMessageProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{

    /**
     *
     * @var JobRunner
     */
    private $jobRunner;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /** @var MemberBillingManager */
    private $billingManager;

    const TOPIC_FEE_CALCULATION = 'dmkfeeaccounting';

    const OPTION_MEMBERBILLING = 'memberbilling_id';

    const OPTION_MEMBERID = 'member_id';

    const OPTION_BILLDATE = 'bill_date';

    public function __construct(JobRunner $jobRunner, EntityManager $em, MemberBillingManager $billingManager, LoggerInterface $logger)
    {
        $this->jobRunner = $jobRunner;
        $this->em = $em;
        $this->billingManager = $billingManager;
        $this->logger = $logger;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Oro\Component\MessageQueue\Client\TopicSubscriberInterface::getSubscribedTopics()
     */
    public static function getSubscribedTopics()
    {
        return [
            self::TOPIC_FEE_CALCULATION
        ];
    }

    /**
     * Processes entity to generate pdf
     *
     * @param MemberFee $item
     * @return MemberFee
     * @throws \RuntimeException
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $result = false;
        $data = JSON::decode($message->getBody());

        if (! isset($data['jobId'], $data[self::OPTION_MEMBERID], $data[self::OPTION_MEMBERBILLING])) {
            $this->logger->critical('Got invalid message', $data);

            return self::REJECT;
        }

        $result = $this->jobRunner->runDelayed($data['jobId'], function () use ($data) {
            try {
                $billDate = new \DateTime(isset($data[self::OPTION_BILLDATE]) ? $data[self::OPTION_BILLDATE] : '');
                $this->calculateBill($data[self::OPTION_MEMBERID], $data[self::OPTION_MEMBERBILLING], $billDate);
            } catch (\Exception $e) {
                $this->logger->critical('Fee calculation failed', [
                    'Exception' => $e->getMessage(),
                    'data' => $data
                ]);
                return false;
            }
            return true;
        });
        return $result ? self::ACK : self::REJECT;
    }

    protected function calculateBill($memberId, $memberBillingId, \DateTime $billDate)
    {
        // Member laden
        /* @var $member \DMKClub\Bundle\MemberBundle\Entity\Member */
        $member = $this->billingManager->getMemberRepository()->findOneById($memberId);
        if (! $member) {
            throw new \InvalidArgumentException('Cannot resolve member with id [' . $memberId . '] .');
        }
        /* @var $memberBilling \DMKClub\Bundle\MemberBundle\Entity\MemberBilling */
        $memberBilling = $this->getMemberBillingRepository()->findOneById($memberBillingId);
        if (! $memberBilling) {
            throw new \InvalidArgumentException('Cannot resolve member billing with id [' . $memberBillingId . '] .');
        }
        // Gibt es für den Member schon eine Fee?
        if (! $this->billingManager->hasFee4Billing($member, $memberBilling)) {
            $memberFee = $this->billingManager->calculateMemberFee($memberBilling, $member);
            if ($memberFee->getPriceTotal() == 0) {
                // Ohne Beitrag muss kein Datensatz angelegt werden
                $this->logger->warning('Calculated fee is zero', ['mbr' => $member->getId(), 'billing' => $memberBilling->getId()]);
                return null;
            }
            $memberFee->setBillDate($billDate);
            $memberFee->setOrganization($memberBilling->getOrganization());
            $memberFee->setOwner($memberBilling->getOwner());
            $this->em->persist($memberFee);
            $this->em->flush();
        }
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberBillingRepository
     */
    protected function getMemberBillingRepository()
    {
        return $this->em->getRepository('DMKClubMemberBundle:MemberBilling');
    }
}
