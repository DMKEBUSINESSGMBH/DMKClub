<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DefaultProcessorSettingsType extends AbstractProcessorSettingsType
{
//    const NAME = 'dmkclub_member_default_processor_settings';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(DefaultProcessor::OPTION_FEE, MoneyType::class, [
            		'required' => true,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.fee.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_ADMISSION, MoneyType::class, [
            		'required' => true,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_ADMISSION.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_DISCOUNT, MoneyType::class, [
            		'required' => false,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_DISCOUNT.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_CHILD, MoneyType::class, [
            		'required' => false,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_CHILD.'.label'
            ])
            ->add(DefaultProcessor::OPTION_AGE_CHILD, IntegerType::class, [
            		'required' => true,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_AGE_CHILD.'.label',
            		'tooltip' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_AGE_CHILD.'.tooltip'
            ])
        ;

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return DefaultProcessorSettingsType::class;
    }
}
