<?php
namespace DMKClub\Bundle\PaymentBundle\Sepa\Iban;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use DMKClub\Bundle\PaymentBundle\Sepa\Iban\OpenIBANException;

/**
 * IBAN validation with service from https://openiban.com/
 */
class OpenIBAN
{
    /** @var ConfigManager */
    private $config;

    public function __construct(ConfigManager $config)
    {
        $this->config = $config;
    }

    /**
     *
     * @param string $iban
     * @return \stdClass|NULL
     * @throws OpenIBANException if IBAN ist not valid
     * @throws \RuntimeException if openiban.com not available
     */
    public function lookupBic($iban)
    {
        if (!$this->config->get('dmk_club_payment.openiban_enable', false)) {
            return null;
        }

        $baseUri = $this->config->get('dmk_club_payment.openiban_baseuri');
        $uri = sprintf('%s/validate/%s?getBIC=true&validateBankCode=true', $baseUri, $iban);
        $result = file_get_contents($uri);

        if (! $result) {
            throw new \RuntimeException('No result from configured openiban server');
        }

        $result = json_decode($result);

        if (! $result->valid) {
            throw new OpenIBANException('IBAN not valid ' . $iban . ' ' . implode(',', $result->messages));
        }

        if ($result->bankData->bankCode) {
            $bankData = $result->bankData;

            $bankMap = new \stdClass();
            $bankMap->bic = $bankData->bic;
            $bankMap->name = $bankData->name;
            return $bankData;
        }
        return null;
    }
}
