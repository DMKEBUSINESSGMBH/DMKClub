<?php
namespace DMKClub\Bundle\PaymentBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

use Symfony\Component\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\ImportExportBundle\File\FileSystemOperator;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;

use DMKClub\Bundle\PaymentBundle\Sepa\DirectDebitBuilder;
use DMKClub\Bundle\PaymentBundle\Sepa\Payment;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaException;
use DMKClub\Bundle\PaymentBundle\Sepa\Transaction;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface;

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
	protected $logger;

	/** @var DirectDebitBuilder */
	protected $sepaBuilder;
	protected $payment;
	/** @var FileSystemOperator */
	protected $fileSystemOperator;
	protected $router;
	protected $attachmentManager;

	/**
	 * @param EntityManager $entityManager
	 * @param TranslatorInterface $translator
	 * @param ServiceLink $securityFacadeLink
	 * @param DirectDebitBuilder $sepaBuilder
	 */
	public function __construct(EntityManager $entityManager, TranslatorInterface $translator,
	       LoggerInterface $logger, $router, DirectDebitBuilder $sepaBuilder,
	       FileSystemOperator $fso, AttachmentManager $attachmentManager) {
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->logger = $logger;
		$this->router = $router;
		$this->sepaBuilder = $sepaBuilder;
		$this->fileSystemOperator = $fso;
		$this->attachmentManager = $attachmentManager;
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
 			$result = $this->handleExport($options, $data, $query);
			$this->entityManager->commit();
		} catch (\Exception $e) {
			$this->entityManager->rollback();

			return new MassActionResponse(
					false,
					$this->translator->trans($e->getMessage())
					,
					[]
			);
		}

		return $this->getResponse($args, $result);
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

		if (array_key_exists('values', $data) && !empty($data['values'])) {
			$entity_ids = explode(',', $data['values']);
			foreach ($entity_ids As $entityId) {
				if($this->handleItem($entityId, $entity_name))
					$iteration++;
			}
		}
		elseif($isAllSelected) {
			$result = $query->iterate();
			foreach ($result as $row) {
				$row = reset($row);
				$entityId = $row['id']; // sollte aus dem data_identifier geholt werden
				if($this->handleItem($entityId, $entity_name))
					$iteration++;
			}
		}
		$ret = [$iteration];
		if($iteration > 0) {
			$xml = $this->sepaBuilder->buildXML();
			$outputFormat = 'xml';
			$fileName = $this->fileSystemOperator->generateTemporaryFileName('sepadirectdebit', $outputFormat);
 			$file = new \SplFileObject($fileName, 'w');
 			$ret[] = $fileName;
 			$bytes = $file->fwrite($xml);
 			$ret[] = $bytes;
			$this->logger->info('SEPA xml file created', ['file' => $fileName, 'bytes' => $bytes, 'items' => $iteration] );
		}
		return $ret;
	}

	private function handleItem($entityId, $entityName) {
		$sepaItem = $this->resolveEntity($entityId, $entityName);
		if(!($sepaItem instanceof SepaDirectDebitAwareInterface))
			throw new SepaException('Entity does not implement SepaDirectDebitAwareInterface');

		if(!$this->sepaBuilder->isInited()) {
			// TODO: holen
			// Wir benötigen Zugriff auf das MemberBilling
			$paymentAware = $sepaItem->getPaymentAware();
			$this->assertCreditor($paymentAware);

			$identifier = $this->getUniqueMessageIdentification($paymentAware);
			$this->sepaBuilder->init($identifier, $paymentAware->getInitiatingPartyName());
			$this->logger->error('Init called');

			$this->payment = new Payment();
			$this->payment->setId($paymentAware->getPaymentId())
				->setCreditorName($paymentAware->getCreditorName())
				->setCreditorAccountIBAN($paymentAware->getCreditorIban())
				->setCreditorAgentBIC($paymentAware->getCreditorBic())
				->setCreditorId($paymentAware->getCreditorId());
			$this->sepaBuilder->addPaymentInfo($this->payment);
		}

		$ret = false;
		if($this->isSepaDirectDebitPossible($sepaItem)) {
			$ret = true;
			$transaction = new Transaction();
			$transaction->setPayment($this->payment)
			->setAmount($sepaItem->getSepaAmount())
			->setDebtorName($sepaItem->getDebtorName())
			->setDebtorBic($sepaItem->getDebtorBic())
			->setDebtorIban($sepaItem->getDebtorIban())
			->setDebtorMandateSignDate($sepaItem->getDebtorMandateSignDate())
			->setRemittanceInformation($sepaItem->getRemittanceInformation());
			$this->sepaBuilder->addPaymentTransaction($transaction);
		}
		return $ret;
	}

	protected function assertCreditor(SepaPaymentAwareInterface $paymentAware) {
		if(!$paymentAware->getCreditorId() || !$paymentAware->getCreditorBic() ||
				!$paymentAware->getCreditorIban()) {
			$this->logger->error('Missing SEPA creditor data' , [
					'creditorid'=>$paymentAware->getCreditorId(),
					'bic'=>$paymentAware->getCreditorBic(),
					'iban'=>$paymentAware->getCreditorIban(),
			]);
			throw new SepaException('Sepa creditor data not valid');
		}
	}
	/**
	 * Generate payment
	 * @param SepaPaymentAwareInterface $paymentAware
	 */
	protected function getUniqueMessageIdentification(SepaPaymentAwareInterface $paymentAware) {
		if($paymentAware->getUniqueMessageIdentification())
			return $paymentAware->getUniqueMessageIdentification();
		$date = new \DateTime();
		return 'dmkclb'.$date->format('YmdHms');
	}
	/**
	 *
	 * @param SepaDirectDebitAwareInterface $sepaItem
	 */
	protected function isSepaDirectDebitPossible(SepaDirectDebitAwareInterface $sepaItem) {
		return $sepaItem->isSepaDirectDebitPossible()
			&& $sepaItem->getDebtorIban()
			&& $sepaItem->getDebtorBic();
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
	protected function isAllSelected($data) {
		return array_key_exists('inset', $data) && $data['inset'] === '0';
	}

	/**
	 * @param MassActionHandlerArgs $args
	 * @param int $entitiesCount
	 *
	 * @return MassActionResponse
	 */
	protected function getResponse(MassActionHandlerArgs $args, $result) {
		$entitiesCount = $result[0];
		$fileName = $entitiesCount ? $result[1] : '';
		$bytes = $entitiesCount ? $result[2] : 0;

		$massAction      = $args->getMassAction();

		$responseMessage = 'dmkclub.payment.datagrid.action.sepa_direct_debit_success_message';
		$responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);
		$successful = $entitiesCount > 0;
		$url = '';
		if($entitiesCount > 0) {
			$url = $this->router->generate(
					'oro_importexport_export_download',
					['fileName' => basename($fileName)]
			);
		}
		$responseData    = [
				'count' => $entitiesCount,
				'bytes' => $bytes,
				'bytes_hr' => $this->attachmentManager->getFileSize($bytes),
				'url' => $url,
		];

		return new MassActionResponse(
				$successful,
				$this->translator->transChoice(
						$responseMessage,
						$entitiesCount,
						['%count%' => $entitiesCount]
				),
				$responseData
		);
	}

}