<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Psr\Log\LoggerInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;

class MemberFeeCorrectionHandler implements MassActionHandlerInterface
{

    const MARK = 'MARK';

    const UNMARK = 'UNMARK';

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

    /** @var AuthorizationCheckerInterface */
    protected $securityFacade;

    /** @var MemberFeeManager */
    protected $feeManager;

    /** @var LoggerInterface */
    protected $logger;

    /**
     *
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        EntityManager $entityManager,
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker,
        MemberFeeManager $feeManager,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->securityFacade = $authorizationChecker;
        $this->feeManager = $feeManager;
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

        $this->entityManager->beginTransaction();
        try {
            set_time_limit(0);
            $iteration = $this->handleFeeCorrection($options, $data, $results);
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->logger->error('Mark/unmark fee for correction failed.', [
                'exception' => $e,
                'options' => $options,
                'stack' => $e->getTraceAsString(),
            ]);
            $this->entityManager->rollback();
            return new MassActionResponse(false, $e->getMessage(), []);
        }

        return $this->getResponse($args, $iteration);
    }

    /**
     *
     * @param array $options
     * @param array $data
     * @param Query $query
     *            Die Query des Datagrids
     * @return int
     */
    protected function handleFeeCorrection($options, $data, IterableResultInterface $results)
    {
        $markType = $options['mark_type'];
        $iteration = 0;
        foreach ($results as $result) {
            /** @var MemberFee $entity */
            $entityId = $result->getValue('id');
            $this->updateFee($entityId, $markType);

            if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            $iteration ++;
        }
        $this->entityManager->flush();

        return $iteration;
    }
    /**
     *
     * @param int $entityId
     * @param string $markType
     */
    protected function updateFee($entityId, $markType)
    {
        $entity = $this->feeManager->getMemberFeeRepository()->find($entityId);

        if ($this->securityFacade->isGranted('EDIT', $entity)) {
            $this->feeManager->setFeeCorrectionStatus($entity, $markType === self::MARK);
        }

        $this->entityManager->persist($entity);
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
        $responseMessage = 'oro.email.datagrid.mark.success_message'; // FIXME!!
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