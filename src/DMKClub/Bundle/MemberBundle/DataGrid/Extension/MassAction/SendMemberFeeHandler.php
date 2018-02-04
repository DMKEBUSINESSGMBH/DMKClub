<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\CronBundle\Async\CommandRunner;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;

use Psr\Log\LoggerInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use DMKClub\Bundle\MemberBundle\Mailer\Processor;
use DMKClub\Bundle\MemberBundle\Command\SendFeeMailsCommand;

class SendMemberFeeHandler implements MassActionHandlerInterface
{
    const FLUSH_BATCH_SIZE = 100;

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /** @var SecurityFacade */
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
     * @param ServiceLink $securityFacadeLink
     */
    public function __construct(
        TranslatorInterface $translator,
        ServiceLink $securityFacadeLink,
        MemberFeeManager $feeManager,
        Processor $mailer,
        CommandRunner $commandRunner,
        LoggerInterface $logger
    )
    {
        $this->translator = $translator;
        $this->securityFacade = $securityFacadeLink->getService();
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

        try {
            set_time_limit(0);
            $result = $this->handleSendMemberFee($options, $data, $args->getResults());
        } catch (\Exception $e) {
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
        $isAllSelected = $this->isAllSelected($data);
        $iteration = 0;

        $feeIds = [];
        if (array_key_exists('values', $data)) {
            $feeIds = explode(',', $data['values']);
        }
        if ($feeIds || $isAllSelected) {
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
        }

        return $iteration;
    }

    protected function scheduleSendCommand($ids)
    {
        $idArg = implode(',', $ids);
        $this->commandRunner->run(SendFeeMailsCommand::NAME, ['ids' => $idArg]);
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