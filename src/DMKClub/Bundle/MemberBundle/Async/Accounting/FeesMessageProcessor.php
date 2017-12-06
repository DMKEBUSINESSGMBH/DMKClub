<?php
namespace DMKClub\Bundle\MemberBundle\Async\Accounting;

use Psr\Log\LoggerInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Oro\Component\MessageQueue\Job\Job;
use Oro\Component\MessageQueue\Job\JobRunner;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use Oro\Component\MessageQueue\Job\DependentJobService;

/**
 * Die Klasse erzeugt die MemberFees.
 * Dazu wird für jede Rechnung ein Child-Jobs erstellt.
 * Am Ende der Verarbeitung wird per DependendJob das Summary der Abrechnung erneuert.
 *
 * @author "René Nitzsche"
 */
class FeesMessageProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{

    /**
     *
     * @var MessageProducerInterface
     */
    private $producer;

    /**
     *
     * @var JobRunner
     */
    private $jobRunner;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     *
     * @var DependentJobService
     */
    protected $dependentJobSrv;

    const TOPIC_FEES_CALCULATION = 'dmkfeesaccounting';

    const OPTION_MEMBERBILLING = 'memberbilling_id';
    const OPTION_ENTITIES = 'entity_ids';
    const OPTION_BILLDATE = 'bill_date';

    public function __construct(MessageProducerInterface $producer, JobRunner $jobRunner, DependentJobService $dependentJob, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->jobRunner = $jobRunner;
        $this->dependentJobSrv = $dependentJob;
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
            self::TOPIC_FEES_CALCULATION
        ];
    }

    /**
     * Processes entity to generate pdf
     *
     * @param MemberFee $item
     * @return MemberFee
     * @throws RuntimeException
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $result = false;
        $data = JSON::decode($message->getBody());
        $ids = isset($data[FeesMessageProcessor::OPTION_ENTITIES]) ? $data[FeesMessageProcessor::OPTION_ENTITIES] : '';
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return self::REJECT;
        }

        asort($ids);
        $jobName = sprintf('%s:%s:%s:%s', self::TOPIC_FEES_CALCULATION, $data[self::OPTION_MEMBERBILLING], $data[self::OPTION_BILLDATE], md5(implode(',', $ids)));

        $result = $this->jobRunner->runUnique( // a root job is creating here
$message->getMessageId(), $jobName, function (JobRunner $jobRunner, Job $job) use ($ids, $data) {
            foreach ($ids as $id) {
                $jobRunner->createDelayed( // child jobs are creating here and get new status
sprintf('%s:bill-%s:%s:mbr-%s', FeeMessageProcessor::TOPIC_FEE_CALCULATION, $data[self::OPTION_MEMBERBILLING], $data[self::OPTION_BILLDATE], $id), function (JobRunner $jobRunner, Job $child) use ($id, $data) {
                    $this->producer->send(FeeMessageProcessor::TOPIC_FEE_CALCULATION, [ // messages for child jobs are sent here
                        FeeMessageProcessor::OPTION_MEMBERID => $id,
                        FeeMessageProcessor::OPTION_MEMBERBILLING => $data[self::OPTION_MEMBERBILLING],
                        FeeMessageProcessor::OPTION_BILLDATE => $data[self::OPTION_BILLDATE],
                        'jobId' => $child->getId() // the created child jobs ids are passing as message body params
                    ]);
                });
            }

            // Zum Abschluß das Summary berechnen
            $this->addDependedJob($job->getRootJob(), $data[self::OPTION_MEMBERBILLING]);

            $this->logger->info(sprintf('Sent "%s" messages', count($ids)), [
                'data' => $data
            ]);

            return true;
        });

        return $result ? self::ACK : self::REJECT;
    }

    /**
     *
     * @param Job $rootJob
     * @param array $body
     */
    protected function addDependedJob(Job $rootJob, int $memberBillingId)
    {
        $context = $this->dependentJobSrv->createDependentJobContext($rootJob);

        $context->addDependentJob(BillingSummaryMessageProcessor::TOPIC_BILL_SUMMARY, [
            'jobId' => $rootJob->getId(),
            BillingSummaryMessageProcessor::OPTION_MEMBERBILLING => $memberBillingId
        ]);

        $this->dependentJobSrv->saveDependentJob($context);
    }
}
