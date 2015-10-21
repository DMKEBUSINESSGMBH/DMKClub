<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	protected function buildPlainFields(FormBuilderInterface $builder, array $options) {
		$builder
			->add('memberCode', 'text', array('required' => true, 'label' => 'dmkclub.member.member_code.label'))
			->add('startDate', 'oro_date', array('required' => false, 'label' => 'dmkclub.member.start_date.label'))
			->add('endDate', 'oro_date', array('required' => false, 'label' => 'dmkclub.member.end_date.label'))
			->add('name', 'text', array('required' => true, 'label' => 'dmkclub.member.name.label'))
			->add('status', 'dmkclub_memberstatus', array('required' => true, 'label' => 'dmkclub.member.status.label'))
			->add('paymentOption', 'dmkclub_paymentoptions', array('required' => true, 'label' => 'dmkclub.member.payment_option.label'))
			->add('isActive')
			->add('isHonorary')
			->add('isFreeOfCharge')
			->add('owner')
			->add('organization')
		;
// 		$builder->add(
// 					'bankAccount',
// 					'dmkclub_bankaccount',
// 					[
// 							'label'    => 'dmkclub.member.bankAccount.label',
// 							'required' => false,
// 					]
// 				);
		$builder->add(
					'contact',
					'orocrm_contact_select',
					[
							'label'    => 'orocrm.sales.b2bcustomer.contact.label',
							'required' => true,
					]
				);
		$builder->add(
				'postalAddress',
				'oro_address',
				[
						'cascade_validation' => true,
						'required'           => false
				]
			);
		}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options){
        // tags
        $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));
        $builder->add(
        		'dataChannel',
        		'orocrm_channel_select_type',
        		[
        				'required' => true,
        				'label'    => 'orocrm.sales.b2bcustomer.data_channel.label',
        				'entities' => [
        						'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
        				],
        		]
        );

    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\Member',
                'cascade_validation' => true,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_member_member';
    }
}
