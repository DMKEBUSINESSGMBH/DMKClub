<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SepaCreditorSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'oro_entity_create_or_select_inline';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dmkclub_sepacreditor_select';
    }
}
