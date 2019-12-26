<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SepaCreditorType extends AbstractType
{
    public function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class)
            ->add('name',
                TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.sepacreditor.name.label'
            ])
                ->add('iban', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.sepacreditor.iban.label'
            ])
            ->add('bic', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.sepacreditor.bic.label'
            ])
            ->add('creditorId', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.sepacreditor.creditor_id.label'
            ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor',
            'intention' => 'sepacreditor',
            'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
            'single_form' => true
        ]);
    }
}

