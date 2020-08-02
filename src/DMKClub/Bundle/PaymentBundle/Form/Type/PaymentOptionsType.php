<?php

namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

use DMKClub\Bundle\PaymentBundle\Provider\PaymentOptionsProvider;

class PaymentOptionsType extends AbstractType
{
    const NAME = 'dmkclub_paymentoptions';

    /**
     * @var PaymentOptionsProvider
     */
    protected $paymentOptionsProvider;

    /**
     * @param PaymentOptionsProvider $paymentOptionsProvider
     */
    public function __construct(PaymentOptionsProvider $paymentOptionsProvider)
    {
        $this->paymentOptionsProvider = $paymentOptionsProvider;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'choices'     => $this->paymentOptionsProvider->getChoices(),
                'multiple'    => false,
                'expanded'    => false,
                'empty_value' => 'dmkclub.payment_option.form.choose',
                'translatable_options' => false
            ]
        );
    }
}
