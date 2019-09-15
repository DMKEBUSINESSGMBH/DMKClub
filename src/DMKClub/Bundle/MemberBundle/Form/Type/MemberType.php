<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;

class MemberType extends AbstractType
{
    const LABEL_PREFIX = 'dmkclub.member.';

	/** @var TranslatorInterface */
	protected $translator;

	/**
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
	}

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
			->add('paymentOption', 'oro_enum_select', [
			    'required' => true,
			    'label' => self::LABEL_PREFIX.'payment_option.label',
			    'enum_code' => PaymentOption::INTERNAL_ENUM_CODE,

			])
			->add('paymentInterval', 'oro_enum_select', [
			    'required' => true,
			    'enum_code' => PaymentInterval::INTERNAL_ENUM_CODE,
			    'label' => self::LABEL_PREFIX.'payment_interval.label'
			])
			->add('isActive', 'checkbox', array(
					'tooltip' => $this->translator->trans('dmkclub.member.isActive.help'),
					'label' => 'dmkclub.member.is_active.label',
					'required' => false))
			->add('isHonorary', 'checkbox', array('required' => false, 'label' => 'dmkclub.member.is_honorary.label'))
			->add('isFreeOfCharge', 'checkbox', array('required' => false, 'label' => 'dmkclub.member.is_free_of_charge.label'))
//			->add('owner')
//			->add('organization')
		;

		}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options){
        // tags disabled in 1.9
//        $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));
			$builder->add(
					'bankAccount',
					'dmkclub_bankaccount',
					[
							'label'    => 'dmkclub.member.bank_account.label',
							'required' => false,
					]
				);
			$builder->add(
					'contact',
					'oro_contact_select',
					[
							'label'    => 'dmkclub.member.contact.label',
							'required' => true,
					]
				);
			$builder->add(
			    'legalContact',
			    'oro_contact_select',
			    [
			        'label'    => 'dmkclub.member.legal_contact.label',
			        'required' => false,
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
			$builder->add(
				'dataChannel',
				'oro_channel_select_type',
				[
					'required' => true,
					'label'    => 'oro.sales.b2bcustomer.data_channel.label',
					'entities' => [
							'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
					],
				]
	        );
	}
	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
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
	public function getName() {
		return $this->getBlockPrefix();
	}
	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'dmkclub_member_member';
	}
}
