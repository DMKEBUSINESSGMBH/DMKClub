<?php

namespace DMKClub\Bundle\PaymentBundle\Sepa;


interface SepaPaymentAwareInterface {

	/**
	 * Maximal 35 Zeichen
	 * CstmrDrctDbtInitn/GrpHdr/MsgId
	 * @return string
	 */
	public function getUniqueMessageIdentification();

	/**
	 * CstmrDrctDbtInitn/GrpHdr/InitgPty/Nm
	 * @return string
	 */
	public function getInitiatingPartyName();

	/**
	 * CstmrDrctDbtInitn/PmtInf/PmtInfId
	 * @return string
	 */
	public function getPaymentId();

	/**
	 * CstmrDrctDbtInitn/PmtInf/Cdtr/Nm
	 * @return string
	 */
	public function getCreditorName();
	/**
	 * CstmrDrctDbtInitn/PmtInf/CdtrAcct/Id/IBAN
	 * @return string
	 */
	public function getCreditorIban();

	/**
	 * CstmrDrctDbtInitn/PmtInf/CdtrAgt/FinInstnId/BIC
	 * @return string
	 */
	public function getCreditorBic();
	/**
	 * CstmrDrctDbtInitn/PmtInf/CdtrSchmeId/Id/PrvtId/Othr/Id
	 * @return string
	 */
	public function getCreditorId();

}
