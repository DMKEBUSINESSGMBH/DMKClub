<?php

namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

use Oro\Bundle\UserBundle\Provider\GenderProvider;

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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices'     => $this->paymentOptionsProvider->getChoices(),
                'multiple'    => false,
                'expanded'    => false,
                'empty_value' => 'dmkclub.payment.form.choose_option',
                'translatable_options' => false
            )
        );
    }
}
