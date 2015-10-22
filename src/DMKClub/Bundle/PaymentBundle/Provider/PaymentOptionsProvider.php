<?php

namespace DMKClub\Bundle\PaymentBundle\Provider;

use Symfony\Component\Translation\TranslatorInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;

class PaymentOptionsProvider
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $choices = array(
        PaymentOption::NONE  => 'dmkclub.payment_option.none',
    		PaymentOption::SEPA_DIRECT_DEBIT   => 'dmkclub.payment_option.sepa_direct_debit',
        PaymentOption::BANKTRANSFER   => 'dmkclub.payment_option.banktransfer',
        PaymentOption::CASH   => 'dmkclub.payment_option.cash',
        PaymentOption::CREDITCARD   => 'dmkclub.payment_option.creditcard',
    );

    /**
     * @var array
     */
    protected $translatedChoices;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        if (null === $this->translatedChoices) {
            $this->translatedChoices = array();
            foreach ($this->choices as $name => $label) {
                $this->translatedChoices[$name] = $this->translator->trans($label);
            }
        }

        return $this->translatedChoices;
    }

    /**
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function getLabelByName($name)
    {
        $choices = $this->getChoices();
        if (!isset($choices[$name])) {
            throw new \LogicException(sprintf('Unknown payment option with name "%s"', $name));
        }

        return $choices[$name];
    }
}
