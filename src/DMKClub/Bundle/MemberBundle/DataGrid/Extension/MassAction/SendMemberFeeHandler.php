<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\Query;

use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\CronBundle\Async\CommandRunner;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;

use Psr\Log\LoggerInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use DMKClub\Bundle\MemberBundle\Mailer\Processor;
use DMKClub\Bundle\MemberBundle\Command\SendFeeMailsCommand;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;

class SendMemberFeeHandler implements MassActionHandlerInterface
{
    const FLUSH_BATCH_SIZE = 80;

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var AuthorizationCheckerInterface */
    protected $securityFacade;

    /** @var MemberFeeManager */
    protected $feeManager;

    /** @var Processor */
    protected $mailer;

    /** @var CommandRunner */
    protected $commandRunner;

    /** @var LoggerInterface */
    protected $logger;

    /**
     *
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker,
        MemberFeeManager $feeManager,
        Processor $mailer,
        CommandRunner $commandRunner,
        LoggerInterface $logger
    )
    {
        $this->translator = $translator;
        $this->securityFacade = $authorizationChecker;
        $this->feeManager = $feeManager;
        $this->mailer = $mailer;
        $this->commandRunner = $commandRunner;
        $this->logger = $logger;
    }

    /**
     * https://github.com/orocommerce/orocommerce/tree/62ce38756ca325cd9ccff708f2f9767accdd71af/src/OroB2B/Bundle/ShoppingListBundle/Datagrid/Extension/MassAction
     *
     * {@inheritdoc}
     *
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
            $result = $this->handleSendMemberFee($options, $data, $results);
        } catch (\Exception $e) {
            $this->logger->error('Send member fee failed.', [
                'exception' => $e,
                'options' => $options,
                'stack' => $e->getTraceAsString(),
            ]);
            return $this->getErrorResponse($args, $e);
        }

        return $this->getResponse($args, $result);
    }

    /**
     *
     * @param array $options
     * @param array $data
     * @param Query $query
     *            Die Query des Datagrids
     * @return [] keys: iteration, errors
     */
    protected function handleSendMemberFee($options, $data, IterableResultInterface $results)
    {
        $iteration = 0;
        $idBag = [];
        foreach ($results as $result) {
            $iteration ++;
            $entityId = $result->getValue('id');
            $idBag[] = $entityId;

            if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
                $this->scheduleSendCommand($idBag);
                $idBag = [];
            }
        }
        if (!empty($idBag)) {
            $this->scheduleSendCommand($idBag);
        }

        return $iteration;
    }

    protected function scheduleSendCommand($ids)
    {
        $batchSet = [80, 40, 20, 10, 5];
        foreach ($batchSet as $batchSize) {
            $chunks = array_chunk($ids, $batchSize);
            $this->logger->debug('BATCHES', ['size' => $batchSize, 'c'=>count($chunks),'chunks' => $chunks]);
            // Wenn der erste passt, dann passen alle
            if ($this->hasValidCommand($chunks[0])) {
                foreach ($chunks as $chunk) {
                    $this->logger->info('SEND CHUNK! '. $batchSize, ['c' => $chunk]);
                    $this->commandRunner->run(SendFeeMailsCommand::NAME, ['ids' => implode(',', $chunk)]);
                }
                break;
            }
        }
    }
    /**
     * Check if maximum command length is lower then 255 chars. This is limit of column "name" in table "oro_message_queue_job_unique"
     * @param array $ids
     */
    protected function hasValidCommand($ids)
    {
        // "oro:cron:run_command:dmkclub:send:fee-ids=32560,3244 => max 255 Zeichen
        $cmd = sprintf('oro:cron:run_command:%s-%s', SendFeeMailsCommand::NAME, 'ids='.implode(',', $ids));
        return strlen($cmd) < 255;
    }

    /**
     *
     * @param MassActionHandlerArgs $args
     * @param int $entitiesCount
     *
     * @return MassActionResponse
     */
    protected function getResponse(MassActionHandlerArgs $args, $entitiesCount)
    {

        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.member.memberfee.action.send_memberfee.success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

        $successful = $entitiesCount > 0;
        $options = [
            'count' => $entitiesCount,
        ];

        return new MassActionResponse($successful, $this->translator->transChoice($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount,
        ]), $options);
    }

    /**
     *
     * @param MassActionHandlerArgs $args
     * @param int $entitiesCount
     *
     * @return MassActionResponse
     */
    protected function getErrorResponse(MassActionHandlerArgs $args, \Exception $e)
    {
        $massAction = $args->getMassAction();
        $responseMessage = 'dmkclub.basics.datagrid.action.error_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][error]', $responseMessage);

        $options = [
            'msg' => $e->getMessage(),
        ];

        return new MassActionResponse(false, $this->translator->trans($responseMessage, [
            '%msg%' => $e->getMessage()
        ]), $options);
    }
}
