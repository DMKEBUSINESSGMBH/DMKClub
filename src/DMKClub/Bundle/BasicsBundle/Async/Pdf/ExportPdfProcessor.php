<?php
namespace DMKClub\Bundle\BasicsBundle\Async\Pdf;

use Doctrine\ORM\EntityManager;
use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use Psr\Log\LoggerInterface;

use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Oro\Component\MessageQueue\Job\JobRunner;

use DMKClub\Bundle\BasicsBundle\Async\Topics;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use DMKClub\Bundle\BasicsBundle\PDF\PdfAwareInterface;

/**
 * Die Klasse erzeugt die PDF Dateien
 *
 */
class ExportPdfProcessor implements MessageProcessorInterface, TopicSubscriberInterface
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
    /**
     * @var FilesystemMap
     */
    protected $fileSystemMap = NULL;
    /**
     * @var Filesystem
     */
    protected $fs = NULL;

    /**
     * @var Manager
     */
    protected $pdfManager;

    const OPTION_ENTITYID = 'entity_id';

    public function __construct(JobRunner $jobRunner, EntityManager $em, Manager $pdfManager, FilesystemMap $fileSystemMap, LoggerInterface $logger)
    {
        $this->jobRunner     = $jobRunner;
        $this->em            = $em;
        $this->pdfManager    = $pdfManager;
        $this->fileSystemMap = $fileSystemMap;
        $this->logger        = $logger;
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
            Topics::EXPORT_PDF_DELAYED
        ];
    }

    /**
     * Processes entity to generate pdf
     *
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $result = false;
        $data = JSON::decode($message->getBody());

        if (! isset($data['jobId'], $data[self::OPTION_ENTITYID], $data[ExportPdfsMessageProcessor::OPTION_ENTITYNAME])) {
            $this->logger->critical('Got invalid message', $data);

            return self::REJECT;
        }

        $result = $this->jobRunner->runDelayed($data['jobId'], function () use ($data) {
            try {
                $entity = $this->resolveEntity($data[self::OPTION_ENTITYID], $data[ExportPdfsMessageProcessor::OPTION_ENTITYNAME]);
                if ($entity instanceof PdfAwareInterface) {
                    /* @var $entity PdfAwareInterface */
                    $filePath = $this->pdfManager->buildPdf($entity);
                    $fs = $this->fileSystemMap->get($entity->getExportFilesystem());
                    $fileName = basename($filePath);
                    $fs->write($fileName, file_get_contents($filePath));
                    unlink($filePath); // Quelldatei löschen
                }
            } catch (\Exception $e) {
                $this->logger->critical('PDF creation failed', [
                    'Exception' => $e->getMessage(),
                    'data' => $data
                ]);
                return false;
            }
            return true;
        });
        return $result ? self::ACK : self::REJECT;
    }

    /**
     *
     * @param int $itemId
     * @param string $entityName
     * @return object|null
     */
    protected function resolveEntity($itemId, $entityName) {
        $repo = $this->em->getRepository($entityName);
        return $repo->findOneById($itemId);
    }
}
