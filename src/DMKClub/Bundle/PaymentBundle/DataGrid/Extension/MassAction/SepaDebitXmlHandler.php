<?php
namespace DMKClub\Bundle\PaymentBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;

use Oro\Component\PhpUtils\Formatter\BytesFormatter;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResultInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use Oro\Bundle\ImportExportBundle\File\FileManager;

use DMKClub\Bundle\PaymentBundle\Sepa\DirectDebitBuilder;
use DMKClub\Bundle\PaymentBundle\Sepa\Payment;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaException;
use DMKClub\Bundle\PaymentBundle\Sepa\Transaction;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaDirectDebitAwareInterface;
use DMKClub\Bundle\PaymentBundle\Sepa\SepaPaymentAwareInterface;
use DMKClub\Bundle\BasicsBundle\Datasource\ORM\NoOrderingIterableResult;

class SepaDebitXmlHandler implements MassActionHandlerInterface
{

    const FLUSH_BATCH_SIZE = 100;
    const RESULT_SELECTED_TOTAL = 'RESULT_SELECTED_TOTAL';
    const RESULT_PROCESSED = 'RESULT_PROCESSED';
    const RESULT_FILE_NAME = 'RESULT_FILE_NAME';
    const RESULT_FILE_BYTES = 'RESULT_FILE_BYTES';

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
    protected $logger;

    /** @var DirectDebitBuilder */
    protected $sepaBuilder;

    protected $payment;

    /** @var FileManager */
    protected $fileManager;

    protected $router;

    /**
     *
     * @param EntityManager $entityManager
     * @param TranslatorInterface $translator
     * @param DirectDebitBuilder $sepaBuilder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        RouterInterface $router,
        DirectDebitBuilder $sepaBuilder,
        FileManager $fm)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->logger = $logger;
        $this->router = $router;
        $this->sepaBuilder = $sepaBuilder;
        $this->fileManager = $fm;
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

        $this->entityManager->beginTransaction();
        try {
            set_time_limit(0);
            $result = $this->handleExport($options, $data, $results);
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->logger->error('Building SEPA file failed.', [
                'exception' => $e,
            ]);
            $this->entityManager->rollback();

            return new MassActionResponse(false, $this->translator->trans($e->getMessage()), []);
        }

        return $this->getResponse($args, $result);
    }

    /**
     *
     * @param array $options
     * @param array $data
     * @param Query $query
     *            Die Query des Datagrids
     * @return int
     */
    protected function handleExport($options, $data, IterableResultInterface $results)
    {
        $processed = 0;
        $totalSelected = 0;

        // $data_identifier = $options['data_identifier'];
        $entity_name = $options['entity_name'];

        foreach ($results as $result) {
            $totalSelected++;
            $entityId = $result->getValue('id');
            if ($this->handleItem($entityId, $entity_name)) {
                $processed++;
            }
        }
        $ret = [
            self::RESULT_SELECTED_TOTAL => $totalSelected,
            self::RESULT_PROCESSED => $processed,
        ];

        if ($processed > 0) {
            $xml = $this->sepaBuilder->buildXML();
            $outputFormat = 'xml';
            $fileName = $this->fileManager->generateFileName('sepadirectdebit', $outputFormat);
            $localFile = $this->fileManager->generateTmpFilePath($fileName);

            $file = new \SplFileObject($localFile, 'w');
            $ret[self::RESULT_FILE_NAME] = $fileName;
            $bytes = $file->fwrite($xml);
            $ret[self::RESULT_FILE_BYTES] = $bytes;
            $this->fileManager->writeFileToStorage($localFile, $fileName);
            $this->logger->alert('SEPA xml file created', [
                'file' => $localFile,
                'bytes' => $bytes,
                'items' => $processed,
                'selected' => $totalSelected,
            ]);
        }
        return $ret;
    }

    private function handleItem($entityId, $entityName): bool
    {
        $sepaItem = $this->resolveEntity($entityId, $entityName);
        if (! ($sepaItem instanceof SepaDirectDebitAwareInterface)) {
            throw new SepaException('Entity does not implement SepaDirectDebitAwareInterface');
        }

        if (! $this->sepaBuilder->isInited()) {
            // Wir benötigen Zugriff auf das MemberBilling
            $paymentAware = $sepaItem->getPaymentAware();
            $this->assertCreditor($paymentAware);

            $identifier = $this->getUniqueMessageIdentification($paymentAware);
            $this->sepaBuilder->init($identifier, $paymentAware->getInitiatingPartyName());
            $this->logger->notice('SEPA builder initiated for {sepa_party}', [
                'sepa_party' => $paymentAware->getInitiatingPartyName(),
            ]);

            $this->payment = new Payment();
            $this->payment->setId($paymentAware->getPaymentId()) // PaymentInfID – eindeutige Referenz der log. Datei
                ->setCreditorName($paymentAware->getCreditorName())
                ->setCreditorAccountIBAN($paymentAware->getCreditorIban())
                ->setCreditorAgentBIC($paymentAware->getCreditorBic())
                ->setCreditorId($paymentAware->getCreditorId());
            $this->sepaBuilder->addPaymentInfo($this->payment);
        }

        $ret = false;
        if ($this->isSepaDirectDebitPossible($sepaItem)) {
            $ret = true;
            $transaction = new Transaction();
            $transaction->setPayment($this->payment)
                ->setAmount($sepaItem->getSepaAmount())
                ->setDebtorName($sepaItem->getDebtorName())
                ->setDebtorBic($sepaItem->getDebtorBic())
                ->setDebtorIban($sepaItem->getDebtorIban())
                ->setDebtorMandate($sepaItem->getDebtorMandate()) // Mandatsreferenz MndtId
                ->setDebtorMandateSignDate($sepaItem->getDebtorMandateSignDate())
                ->setRemittanceInformation($sepaItem->getRemittanceInformation());
            $this->sepaBuilder->addPaymentTransaction($transaction);
        }
        return $ret;
    }

    protected function assertCreditor(SepaPaymentAwareInterface $paymentAware)
    {
        if (! $paymentAware->getCreditorId() || ! $paymentAware->getCreditorBic() || ! $paymentAware->getCreditorIban()) {
            $this->logger->error('Missing SEPA creditor data', [
                'creditorid' => $paymentAware->getCreditorId(),
                'bic' => $paymentAware->getCreditorBic(),
                'iban' => $paymentAware->getCreditorIban()
            ]);
            throw new SepaException('Sepa creditor data not valid');
        }
    }

    /**
     * Generate payment
     *
     * @param SepaPaymentAwareInterface $paymentAware
     */
    protected function getUniqueMessageIdentification(SepaPaymentAwareInterface $paymentAware)
    {
        if ($paymentAware->getUniqueMessageIdentification()) {
            return $paymentAware->getUniqueMessageIdentification();
        }
        $date = new \DateTime();
        return 'dmkclb' . $date->format('YmdHms');
    }

    /**
     *
     * @param SepaDirectDebitAwareInterface $sepaItem
     */
    protected function isSepaDirectDebitPossible(SepaDirectDebitAwareInterface $sepaItem)
    {
        return $sepaItem->isSepaDirectDebitPossible() &&
            $sepaItem->getDebtorIban() &&
            $sepaItem->getDebtorBic() &&
            $sepaItem->getDebtorMandateSignDate();
    }

    /**
     *
     * @param int $itemId
     * @param string $entityName
     * @return object|null
     */
    protected function resolveEntity($entityId, $entityName)
    {
        $repo = $this->entityManager->getRepository($entityName);
        return $repo->findOneById($entityId);
    }

    /**
     *
     * @param MassActionHandlerArgs $args
     * @param int $entitiesCount
     *
     * @return MassActionResponse
     */
    protected function getResponse(MassActionHandlerArgs $args, $result)
    {
        $entitiesCount = $result[self::RESULT_PROCESSED];
        $fileName = $entitiesCount ? $result[self::RESULT_FILE_NAME] : '';
        $bytes = $entitiesCount ? $result[self::RESULT_FILE_BYTES] : 0;

        $massAction = $args->getMassAction();

        $responseMessage = 'dmkclub.payment.datagrid.action.sepa_direct_debit_success_message';
        $responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);
        $successful = $entitiesCount > 0;
        $url = '';
        if ($entitiesCount > 0) {
            $url = $this->router->generate('dmkclub_basics_export_download', [
                'fileName' => basename($fileName)
            ]);
        }
        $responseData = [
            'count' => $entitiesCount,
            'bytes' => $bytes,
            'bytes_hr' => BytesFormatter::format($bytes),
            'url' => $url
        ];

        return new MassActionResponse($successful, $this->translator->transChoice($responseMessage, $entitiesCount, [
            '%count%' => $entitiesCount
        ]), $responseData);
    }
}