<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class SimpleProcessorSettingsType extends AbstractProcessorSettingsType
{
    const NAME = 'dmkclub_member_simple_processor_settings';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fee', MoneyType::class, [
            'required' => true,
            'divisor' => 100,
            'label' => 'dmkclub.member.memberbilling.fee.label'
        ]);

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
        return SimpleProcessorSettingsType::class;
    }
}
