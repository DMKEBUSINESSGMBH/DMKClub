<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\OroDateType;

class BankAccountType extends AbstractType
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
            ->add('accountOwner', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.bankaccount.account_owner.label'
            ])
            ->add('iban', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.bankaccount.iban.label',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Iban()
                ]
            ])
            ->add('bic', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.bankaccount.bic.label',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Bic()
                ]
            ])
            ->add('bankName', TextType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.bankaccount.bank_name.label'
            ])
            ->add('directDebitValidFrom', OroDateType::class, [
                'required' => false,
                'label' => 'dmkclub.payment.bankaccount.direct_debit_valid_from.label'
            ]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\PaymentBundle\Entity\BankAccount',
            'intention' => 'bankaccount',
            'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
            'single_form' => true
        ]);
    }
}

