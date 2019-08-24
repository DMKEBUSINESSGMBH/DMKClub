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

/**
 * Calculate summary for member billing
 *
 * @author "René Nitzsche"
 */
class BillingSummaryMessageProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /** @var MemberBillingManager */
    private $billingManager;

    const TOPIC_BILL_SUMMARY = 'dmk.member.accounting.summary';

    const OPTION_MEMBERBILLING = 'memberbilling_id';

    public function __construct(EntityManager $em, MemberBillingManager $billingManager, LoggerInterface $logger)
    {
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
            self::TOPIC_BILL_SUMMARY
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

        if (! isset($data[self::OPTION_MEMBERBILLING])) {
            $this->logger->critical('Got invalid message', $data);

            return self::REJECT;
        }

        $memberBillingId = $data[self::OPTION_MEMBERBILLING];
        /* @var $memberBilling \DMKClub\Bundle\MemberBundle\Entity\MemberBilling */
        $memberBilling = $this->getMemberBillingRepository()->findOneById($memberBillingId);
        if (! $memberBilling) {
            $this->logger->critical('Cannot resolve member billing with id [' . $memberBillingId . '] .');
            return self::REJECT;
        }
        $this->billingManager->updateSummary($memberBilling);

        return $result ? self::ACK : self::REJECT;
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
