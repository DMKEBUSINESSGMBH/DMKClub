<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;

class SepaCreditorSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'dmkclub_sepacreditors', // für oro searchhandler
                'create_form_route'  => 'dmkclub_sepacreditor_create',
                'configs'            => [
                    'placeholder' => 'dmkclub.payment.sepacreditor.form.choose'
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }
}
