<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use DMKClub\Bundle\BasicsBundle\Async\Topics;
use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;

/**
 * Generic handler to export a combined PDF to a defined filesystem. The source of PDF is created by callback.
 */
class ExportPdfHandler implements MassActionHandlerInterface
{

    const FLUSH_BATCH_SIZE = 100;

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var MemberFeeManager */
    protected $feeManager;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var MessageProducerInterface */
    private $messageProducer;

    /**
     *
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param MessageProducerInterface $messageProducer
     * @param MemberFeeManager $feeManager
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, MessageProducerInterface $messageProducer, MemberFeeManager $feeManager)
    {
        $this->translator       = $translator;
        $this->logger           = $logger;
        $this->messageProducer  = $messageProducer;
        $this->feeManager       = $feeManager;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function handle(MassActionHandlerArgs $args)
    {
        $data = $args->getData();
        $massAction = $args->getMassAction();
        $options = $massAction->getOptions()->toArray();
        $queryBuilder = $args->getResults()->getSource();
        $results      = new NoOrderingIterableResult($queryBuilder);
        $results->setBufferSize(self::FLUSH_BATCH_SIZE);

        try {
            set_time_limit(0);
            $iteration = $this->handleExport($options, $data, $results);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->getResponse($args, $iteration);
    }

    /**
     *
     * @param array $options
     * @param array $data
     * @param IterableResultInterface $results
     * @return int
     */
    protected function handleExport($options, $data, IterableResultInterface $results)
    {
        $jobData = [
            'entity_name' => $options['entity_name']
        ];

        $entityIds = [];
        foreach ($results as $result) {
            $entityIds[] = $result->getValue('id');
        }
        $jobData['entity_ids'] = implode(',', $entityIds);

        if (count($entityIds) > 0) {
//             $jobType = 'export';
//             $jobName = 'dmkexportpdf';
            $this->messageProducer->send(Topics::EXPORT_PDF, $jobData); //($jobType, $jobName, $jobData, true);
        }

        return count($entityIds);
    }

    /**
     *
     * @param MassActionHandlerArgs $args
     * @param int $entitiesCount
     *
     * @return MassActionResponse
     */
    protected function getResponse(MassActionHandlerArgs $args, $entitiesCount = 0)
    {
        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.basics.datagrid.action.success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

        $successful = $entitiesCount > 0;
        $options = [
            'count' => $entitiesCount
        ];

        return new MassActionResponse($successful, $this->translator->transChoice($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount
        ]), $options);
    }
}
