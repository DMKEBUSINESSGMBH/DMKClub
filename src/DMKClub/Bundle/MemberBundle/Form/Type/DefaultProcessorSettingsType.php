<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;

class DefaultProcessorSettingsType extends AbstractProcessorSettingsType
{
    const NAME = 'dmkclub_member_default_processor_settings';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(DefaultProcessor::OPTION_FEE, 'money', [
            		'required' => true,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.fee.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_ADMISSION, 'money', [
            		'required' => true,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_ADMISSION.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_DISCOUNT, 'money', [
            		'required' => false,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_DISCOUNT.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_CHILD, 'money', [
            		'required' => false,
            		'divisor' => 100,
            		'label' => 'dmkclub.member.memberbilling.'.DefaultProcessor::OPTION_FEE_CHILD.'.label'
            ])
            ->add(DefaultProcessor::OPTION_AGE_CHILD, 'integer', [
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return self::NAME;
    }
}
