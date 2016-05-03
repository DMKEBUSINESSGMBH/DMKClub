<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;

/**
 * Base-Exception for accounting errors
 */
class AccountingException extends \RuntimeException {
	public function __construct($message, $code=NULL, $previous=NULL) {
		parent::__construct($message, $code, $previous);
	}

	/* (non-PHPdoc)
	 * @see RuntimeException::getMessage()
	 */
	public function getFullMessage() {
		$msg = $this->getMessage();
		if($e = $this->getPrevious()) {
			$msg .= ' - WAS EXCEPTION: '.$e->getMessage();
		}
		return $msg;
	}

}
