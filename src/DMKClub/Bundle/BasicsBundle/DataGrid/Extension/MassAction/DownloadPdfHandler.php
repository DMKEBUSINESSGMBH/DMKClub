<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;

use DMKClub\Bundle\BasicsBundle\PDF\Manager;

/**
 * Generic handler to download a combined PDF. The source of PDF is created by callback.
 */
class DownloadPdfHandler implements MassActionHandlerInterface
{

    const FLUSH_BATCH_SIZE = 100;

    /**
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \DMKClub\Bundle\BasicsBundle\PDF\Manager */
    private $pdfManager;

    protected $router;

    /**
     *
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $entityManager, TranslatorInterface $translator, LoggerInterface $logger, Manager $pdfManager, $router)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->router = $router;
        $this->pdfManager = $pdfManager;
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

        $this->entityManager->beginTransaction();
        try {
            set_time_limit(0);
            $data = $this->handleExport($options, $data, $args->getResults());
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->logger->error('Downloading pdf failed.', [
                'exception' => $e,
                'options' => $options,
            ]);
            $this->entityManager->rollback();
            return new MassActionResponse(false, $e->getMessage(), []);
        }

        return $this->getResponse($args, $data);
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
        $isAllSelected = $this->isAllSelected($data);
        $iteration = 0;

        $jobData = [
            'data_identifier' => $options['data_identifier'],
            'entity_name' => $options['entity_name']
        ];

        if (array_key_exists('values', $data) && ! empty($data['values'])) {
            $jobData['entity_ids'] = $data['values'];
            $iteration = count(explode(',', $data['values']));
        } elseif ($isAllSelected) {
            $entityIds = [];
            foreach ($results as $result) {
                $entityIds[] = $result->getValue('id');
            }
            $jobData['entity_ids'] = implode(',', $entityIds);

            // $this->entityManager->flush();
            $iteration ++;
        }
        if (array_key_exists('entity_ids', $jobData)) {

            $ids = explode(',', $jobData['entity_ids']);

            $file = $this->pdfManager->buildPdfCombined(function ($pdfCallBack) use ($ids) {
                foreach ($ids as $id) {
                    $memberFee = $this->getMemberFeeRepository()
                        ->findOneBy([
                        'id' => $id
                    ]);
                    $pdfCallBack($memberFee);
                }
            });
        }

        return [
            'items' => $iteration,
            'filename' => $file->getKey()
        ];
    }

    /**
     *
     * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
     */
    public function getMemberFeeRepository()
    {
        return $this->entityManager->getRepository('DMKClubMemberBundle:MemberFee');
    }

    /**
     *
     * @param array $data
     * @return bool
     */
    protected function isAllSelected($data)
    {
        return array_key_exists('inset', $data) && $data['inset'] === '0';
    }

    /**
     *
     * @param MassActionHandlerArgs $args
     * @param int $entitiesCount
     *
     * @return MassActionResponse
     */
    protected function getResponse(MassActionHandlerArgs $args, $data = 0)
    {
        $entitiesCount = $data['items'];
        $fileName = $data['filename'];

        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.basics.datagrid.action.success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

        $successful = $entitiesCount > 0;
        $options = [
            'count' => $entitiesCount
        ];

        $url = '';
        if ($entitiesCount > 0) {
            $url = $this->router->generate('oro_importexport_export_download', [
                'fileName' => basename($fileName)
            ]);
        }
        $options['url'] = $url;

        return new MassActionResponse($successful, $this->translator->transChoice($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount
        ]), $options);
    }
}
