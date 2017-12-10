<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

class SepaCreditorType extends AbstractType
{

    const NAME = 'dmkclub_sepacreditor';

    public function __construct()
    {}

    /**
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden')
            ->add('name', 'text', array(
            'required' => false,
            'label' => 'dmkclub.payment.sepacreditor.name.label'
        ))
            ->add('iban', 'text', array(
            'required' => false,
            'label' => 'dmkclub.payment.sepacreditor.iban.label'
        ))
            ->add('bic', 'text', array(
            'required' => false,
            'label' => 'dmkclub.payment.sepacreditor.bic.label'
        ))
            ->add('creditorId', 'text', array(
            'required' => false,
            'label' => 'dmkclub.payment.sepacreditor.creditor_id.label'
        ));
    }

    /**
     *
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor',
            'intention' => 'sepacreditor',
            'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
            'single_form' => true
        ));
    }
}

