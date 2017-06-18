<?php

namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

use DMKClub\Bundle\PaymentBundle\Provider\PaymentIntervalsProvider;

class PaymentIntervalsType extends AbstractType
{
    const NAME = 'dmkclub_paymentintervals';

    /**
     * @var PaymentIntervalsProvider
     */
    protected $paymentIntervalsProvider;

    /**
     * @param PaymentOptionsProvider $paymentIntervalProvider
     */
    public function __construct(PaymentIntervalsProvider $paymentIntervalProvider)
    {
        $this->paymentIntervalsProvider = $paymentIntervalProvider;
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
                'choices'     => $this->paymentIntervalsProvider->getChoices(),
                'multiple'    => false,
                'expanded'    => false,
                'empty_value' => 'dmkclub.payment_interval.form.choose',
                'translatable_options' => false
            )
        );
    }
}
