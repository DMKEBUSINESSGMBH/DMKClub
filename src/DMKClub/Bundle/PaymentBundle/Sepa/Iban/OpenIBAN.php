<?php

namespace DMKClub\Bundle\PaymentBundle\Sepa\Iban;


use DMKClub\Bundle\PaymentBundle\Sepa\Iban\OpenIBANException;

/**
 * IBAN validation with service from https://openiban.com/
 */
class OpenIBAN {

    /**
     *
     * @param string $iban
     * @return \stdClass|NULL
     * @throws OpenIBANException if IBAN ist not valid
     * @throws \RuntimeException if openiban.com not available
     */
    public function lookupBic($iban) {
        $result = file_get_contents('https://openiban.com/validate/'.$iban.'?getBIC=true&validateBankCode=true');

        if(!$result) {
            throw new \RuntimeException('No result from openiban.com');
        }

        $result = json_decode($result);

        if(!$result->valid) {
            throw new OpenIBANException('IBAN not valid ' . $iban . ' '.implode(',', $result->messages));
        }

        if($result->bankData->bankCode) {

            $bankData = $result->bankData;

            $bankMap = new \stdClass();
            $bankMap->bic = $bankData->bic;
            $bankMap->name = $bankData->name;
            return $bankData;
        }
        return null;
    }
}
