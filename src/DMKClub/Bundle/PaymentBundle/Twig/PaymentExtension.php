<?php

namespace DMKClub\Bundle\PaymentBundle\Twig;

use DMKClub\Bundle\PaymentBundle\Provider\PaymentOptionsProvider;
use DMKClub\Bundle\PaymentBundle\Provider\PaymentIntervalsProvider;

class PaymentExtension extends \Twig_Extension
{
    /**
     * @var PaymentOptionsProvider
     */
    protected $paymentOptionProvider;

    /**
     * @var PaymentIntervalsProvider
     */
    protected $paymentIntervalProvider;

    /**
     * @param MemberStatusProvider $paymentOptionProvider
     */
    public function __construct(PaymentOptionsProvider $paymentOptionProvider, PaymentIntervalsProvider $paymentIntervalProvider)
    {
        $this->paymentOptionProvider = $paymentOptionProvider;
        $this->paymentIntervalProvider = $paymentIntervalProvider;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            'dmkclub_paymentoption' => new \Twig_Function_Method($this, 'getPaymentOptionLabel'),
            'dmkclub_paymentinterval' => new \Twig_Function_Method($this, 'getPaymentIntervalLabel'),
        ];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPaymentOptionLabel($name)
    {
        if (!$name) {
            return null;
        }

        return $this->paymentOptionProvider->getLabelByName($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getPaymentIntervalLabel($name)
    {
        if (!$name) {
            return null;
        }

        return $this->paymentIntervalProvider->getLabelByName($name);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_payment';
    }
}
