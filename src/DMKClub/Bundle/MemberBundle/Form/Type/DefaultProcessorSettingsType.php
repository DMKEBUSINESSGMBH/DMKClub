<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
					->add('fee', 'money', array('required' => true, 'divisor' => 100, 'label' => 'dmkclub.member.memberbilling.fee.label'))
					->add('fee_reduced', 'money', array('required' => false, 'divisor' => 100, 'label' => 'dmkclub.member.memberbilling.fee_reduced.label'))
					->add('age_reduced', 'integer', array('required' => true, 'label' => 'dmkclub.member.memberbilling.age_reduced.label'))
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
//                'data_class' => 'OroCRM\Bundle\CampaignBundle\Entity\InternalTransportSettings'
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
