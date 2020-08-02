<?php
namespace DMKClub\Bundle\BasicsBundle\Async\Pdf;

use Psr\Log\LoggerInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Oro\Component\MessageQueue\Job\Job;
use Oro\Component\MessageQueue\Job\JobRunner;

use DMKClub\Bundle\BasicsBundle\Async\Topics;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

/**
 * This class creates pdf files and exports to configured filesystem.
 *
 * @author "René Nitzsche"
 */
class ExportPdfsMessageProcessor implements MessageProcessorInterface, TopicSubscriberInterface
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

    const OPTION_ENTITIES = 'entity_ids';
    const OPTION_ENTITYNAME = 'entity_name';

    public function __construct(MessageProducerInterface $producer, JobRunner $jobRunner, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->jobRunner = $jobRunner;
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
            Topics::EXPORT_PDF
        ];
    }

    /**
     * Processes entity to generate pdf
     *
     * @param MemberFee $item
     * @return MemberFee
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $result = false;
        $data = JSON::decode($message->getBody());
        $ids = isset($data[self::OPTION_ENTITIES]) ? $data[self::OPTION_ENTITIES] : '';
        $ids = explode(',', $ids);
        if (empty($ids)) {
            return self::REJECT;
        }

        asort($ids);
        $jobName = sprintf('%s:%s:%s', Topics::EXPORT_PDF, $data[self::OPTION_ENTITYNAME], md5(implode(',', $ids)));

        $result = $this->jobRunner->runUnique( // a root job is creating here
            $message->getMessageId(), $jobName, function (JobRunner $jobRunner, Job $job) use ($ids, $data) {
                foreach ($ids as $id) {
                    $jobRunner->createDelayed( // child jobs are creating here and get new status
                        sprintf('%s:bill-%s:%s', Topics::EXPORT_PDF_DELAYED, $data[self::OPTION_ENTITYNAME], $id), function (JobRunner $jobRunner, Job $child) use ($id, $data) {
                        $this->producer->send(Topics::EXPORT_PDF_DELAYED, [ // messages for child jobs are sent here
                            ExportPdfProcessor::OPTION_ENTITYID => $id,
                            self::OPTION_ENTITYNAME => $data[self::OPTION_ENTITYNAME],
                            'jobId' => $child->getId() // the created child jobs ids are passing as message body params
                        ]);
                    });
                }

                $this->logger->info(sprintf('Sent "%s" messages', count($ids)), [
                    'data' => $data
                ]);

                return true;
            }
        );

        return $result ? self::ACK : self::REJECT;
    }

}
