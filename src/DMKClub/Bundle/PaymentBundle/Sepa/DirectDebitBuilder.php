<?php
namespace DMKClub\Bundle\PaymentBundle\Sepa;

use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\TransferFile\Facade\CustomerDirectDebitFacade;

class DirectDebitBuilder
{

    /**
     *
     * @var CustomerDirectDebitFacade
     */
    private $directDebit = NULL;

    private $paymentNames = [];

    public function init($uniqueMessageIdentification, $initiatingPartyName, $painFormat = 'pain.008.002.02')
    {
        $this->directDebit = TransferFileFacadeFactory::createDirectDebit($uniqueMessageIdentification, $initiatingPartyName);
    }

    /**
     *
     * @param
     *            $paymentName
     * @throws \Digitick\Sepa\Exception\InvalidArgumentException
     */
    public function addPaymentInfo(Payment $payment)
    {
        $this->assertInited();
        $this->directDebit->addPaymentInfo($payment->getId(), [
            'id' => $payment->getId(),
            'creditorName' => $payment->getCreditorName(),
            'creditorAccountIBAN' => $payment->getCreditorAccountIBAN(),
            'creditorAgentBIC' => $payment->getCreditorAgentBIC(),
            'seqType' => $payment->getSeqType(),
            'creditorId' => $payment->getCreditorId()
        ]);
    }

    public function addPaymentTransaction(Transaction $transaction)
    {
        $this->directDebit->addTransfer($transaction->getPayment()
            ->getId(), [
            'amount' => $transaction->getAmount(),
            'debtorIban' => $transaction->getDebtorIban(),
            'debtorBic' => $transaction->getDebtorBic(),
            'debtorName' => $transaction->getDebtorName(),
            'debtorMandate' => $transaction->getDebtorMandate(),
            // TODO: check date!
            'debtorMandateSignDate' => $transaction->getDebtorMandateSignDate(),
            'remittanceInformation' => $transaction->getRemittanceInformation()
        ]);
    }

    public function buildXml()
    {
        return $this->directDebit->asXML();
    }

    /**
     *
     * @return boolean whether or not init() was called
     */
    public function isInited()
    {
        return $this->directDebit !== NULL;
    }

    private function assertInited()
    {
        if (! $this->isInited())
            throw new \Exception('Call init() before any other method!');
    }
}
