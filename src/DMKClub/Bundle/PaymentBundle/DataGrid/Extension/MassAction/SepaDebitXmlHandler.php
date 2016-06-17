<?php
namespace DMKClub\Bundle\PaymentBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\AbstractQuery;
use Akeneo\Bundle\BatchBundle\Entity\JobInstance;
use DMKClub\Bundle\BasicsBundle\Job\JobExecutor;
use DMKClub\Bundle\PaymentBundle\Sepa\DirectDebitBuilder;
use DMKClub\Bundle\PaymentBundle\Sepa\Payment;
use DMKClub\Bundle\PaymentBundle\Sepa\Transaction;

class SepaDebitXmlHandler implements MassActionHandlerInterface {
	const FLUSH_BATCH_SIZE = 100;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/** @var \Psr\Log\LoggerInterface */
	private $logger;

	/** @var DirectDebitBuilder */
	private $sepaBuilder;
	private $payment;

	/**
	 * @param EntityManager $entityManager
	 * @param TranslatorInterface $translator
	 * @param ServiceLink $securityFacadeLink
	 * @param DirectDebitBuilder $sepaBuilder
	 */
	public function __construct(EntityManager $entityManager, TranslatorInterface $translator,
			LoggerInterface $logger, DirectDebitBuilder $sepaBuilder) {
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->logger = $logger;
		$this->sepaBuilder = $sepaBuilder;
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

//		$data_identifier = $options['data_identifier'];
		$entity_name =$options['entity_name'];

		// TODO: implement

		if (array_key_exists('values', $data) && !empty($data['values'])) {
			$entity_ids = explode(',', $data['values']);
			$iteration = count($entity_ids);
			foreach ($entity_ids As $entityId) {
				$this->handleItem($entityId, $entity_name);
			}
		}
		elseif($isAllSelected) {
			$result = $query->iterate();
			foreach ($result as $row) {
				$row = reset($row);
				$entityId = $row['id'];
				$this->handleItem($entityId, $entity_name);
				$iteration++;
			}
		}
		if($iteration > 0) {
			$xml = $this->sepaBuilder->buildXML();
			$this->logger->error('SEPA fertig', ['XML' => $xml] );
		}

		return $iteration;
	}

	private function handleItem($entityId, $entityName) {
		if(!$this->sepaBuilder->isInited()) {
			// TODO: holen
			$identifier = '4567';
			$this->sepaBuilder->init($identifier, 'My Club');
			$this->logger->error('Init called');

			$this->payment = new Payment();
			$this->payment->setId('123')
				->setCreditorName('My Club e.V.')
				->setCreditorAccountIBAN('DE345678989998')
				->setCreditorAgentBIC('CHEKDE81XXX')
				->setCreditorId('DE87WRE8R');
			$this->sepaBuilder->addPaymentInfo($this->payment);
		}
		$sepaItem = $this->resolveEntity($entityId, $entityName);

		$transaction = new Transaction();
		$transaction->setPayment($this->payment)
			->setAmount('500')
			->setDebtorName('Hans Testus')
			->setDebtorBic('DEUTDEDBCHE')
			->setDebtorIban('DE29870700240001641955')
			->setDebtorMandateSignDate('13.10.2016')
			->setRemittanceInformation('Mitgliedsbeitrag Verein');
		$this->sepaBuilder->addPaymentTransaction($transaction);

	}

	/**
	 *
	 * @param int $itemId
	 * @param string $entityName
	 * @return object|null
	 */
	protected function resolveEntity($entityId, $entityName) {
		$repo = $this->entityManager->getRepository($entityName);
		return $repo->findOneById($entityId);
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
		$responseMessage = 'dmkclub.payment.datagrid.action.sepa_direct_debit_success_message';
		$responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

		$successful = $entitiesCount > 0;
		$options    = ['count' => $entitiesCount];
		$options['url'] = 'fieldaet';

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