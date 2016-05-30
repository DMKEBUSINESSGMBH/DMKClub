<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use Doctrine\ORM\Query;

class ExportPdfHandler implements MassActionHandlerInterface {
	const FLUSH_BATCH_SIZE = 100;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/** @var MemberFeeManager */
	protected $feeManager;

	/**
	 * @param EntityManager $entityManager
	 * @param TranslatorInterface $translator
	 * @param ServiceLink $securityFacadeLink
	 */
	public function __construct(
			EntityManager $entityManager,
			TranslatorInterface $translator,
			MemberFeeManager $feeManager
	) {
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->feeManager = $feeManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(MassActionHandlerArgs $args) {
		$data = $args->getData();
		$massAction = $args->getMassAction();
		$options = $massAction->getOptions()->toArray();

 		$query = $args->getResults()->getSource();
// 		$query->getQuery()->getAST()->

		$this->entityManager->beginTransaction();
		try {
			set_time_limit(0);
 			$iteration = $this->handleExport($options, $data, $query);
			$this->entityManager->commit();
		} catch (\Exception $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		return $this->getResponse($args, $iteration);
	}

	/**
	 * @param array $options
	 * @param array $data
	 * @param Query $query Die Query des Datagrids
	 * @return int
	 */
	protected function handleExport($options, $data, $query) {
		$isAllSelected = $this->isAllSelected($data);
		$iteration = 0;

		$feeIds = [];
		if (array_key_exists('values', $data)) {
			$feeIds = explode(',', $data['values']);
		}
		if ($feeIds || $isAllSelected) {
			$result = $query->iterate();
			foreach ($result as $row) {
				/** @var MemberFee $entity */
				$entity = reset($row);
				$entity = $this->feeManager->getMemberFeeRepository()->find($entity['id']);
				// TODO: Batch-Job erstellen

//				$this->entityManager->persist($entity);

				if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
// 					$this->entityManager->flush();
// 					$this->entityManager->clear();
				}
				$iteration++;
			}

//			$this->entityManager->flush();
		}

		return $iteration;
	}
	/**
	 * @param array $data
	 * @return bool
	 */
	protected function isAllSelected($data)
	{
		return array_key_exists('inset', $data) && $data['inset'] === '0';
	}

	/**
	 * @param MassActionHandlerArgs $args
	 * @param int $entitiesCount
	 *
	 * @return MassActionResponse
	 */
	protected function getResponse(MassActionHandlerArgs $args, $entitiesCount = 0)
	{
		$massAction      = $args->getMassAction();
		$responseMessage = 'dmkclub.basics.datagrid.action.success_message';
		$responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

		$successful = $entitiesCount > 0;
		$options    = ['count' => $entitiesCount];

		return new MassActionResponse(
				$successful,
				$this->translator->transChoice(
						$responseMessage,
						$entitiesCount,
						['%count%' => $entitiesCount]
				),
				$options
		);
	}

}