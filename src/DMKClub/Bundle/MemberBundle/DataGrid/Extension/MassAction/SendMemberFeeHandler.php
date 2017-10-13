<?php
namespace DMKClub\Bundle\MemberBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use Doctrine\ORM\Query;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;

class SendMemberFeeHandler implements MassActionHandlerInterface
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

    /** @var SecurityFacade */
    protected $securityFacade;

    /** @var MemberFeeManager */
    protected $feeManager;

    /**
     *
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     * @param ServiceLink $securityFacadeLink
     */
    public function __construct(EntityManager $entityManager, TranslatorInterface $translator, ServiceLink $securityFacadeLink, MemberFeeManager $feeManager)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->securityFacade = $securityFacadeLink->getService();
        $this->feeManager = $feeManager;
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

        $this->entityManager->beginTransaction();
        try {
            set_time_limit(0);
            $iteration = $this->handleSendMemberFee($options, $data, $args->getResults());
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
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
    protected function handleSendMemberFee($options, $data, IterableResultInterface $results)
    {
        $isAllSelected = $this->isAllSelected($data);
        $iteration = 0;

        $feeIds = [];
        if (array_key_exists('values', $data)) {
            $feeIds = explode(',', $data['values']);
        }
        if ($feeIds || $isAllSelected) {
            foreach ($results as $result) {
                /** @var MemberFee $entity */
                $entityId = $result->getValue('id');
                $entity = $this->feeManager->getMemberFeeRepository()->find($entityId);

                // SEND EMAIL

                // $this->entityManager->persist($entity);

                if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
                $iteration ++;
            }

            $this->entityManager->flush();
        }

        return $iteration;
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