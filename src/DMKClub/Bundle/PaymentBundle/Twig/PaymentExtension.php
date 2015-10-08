<?php

namespace DMKClub\Bundle\PaymentBundle\Twig;

use DMKClub\Bundle\PaymentBundle\Provider\PaymentOptionsProvider;

class PaymentExtension extends \Twig_Extension
{
    /**
     * @var MemberStatusProvider
     */
    protected $paymentOptionProvider;

    /**
     * @param MemberStatusProvider $paymentOptionProvider
     */
    public function __construct(PaymentOptionsProvider $paymentOptionProvider)
    {
        $this->paymentOptionProvider = $paymentOptionProvider;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'dmkclub_paymentoption' => new \Twig_Function_Method($this, 'getPaymentOptionLabel'),
        );
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
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_payment';
    }
}
