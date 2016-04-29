<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleProcessorSettingsType extends AbstractProcessorSettingsType
{
    const NAME = 'dmkclub_member_simple_processor_settings';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
					->add('fee', 'money', array('required' => true, 'divisor' => 100, 'label' => 'dmkclub.member.memberbilling.fee.label'))
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
