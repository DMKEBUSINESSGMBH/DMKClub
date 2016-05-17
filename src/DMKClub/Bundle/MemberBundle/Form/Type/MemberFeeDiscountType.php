<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MemberFeeDiscountType extends AbstractType {

	/** @var TranslatorInterface */
	protected $translator;

	/**
	 * @param ConfigManager       $configManager
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
			->add('reason', 'text', array('required' => true, 'label' => 'dmkclub.member.memberfeediscount.reason.label'))
			->add('startDate', 'oro_date', array('required' => true, 'label' => 'dmkclub.member.memberfeediscount.start_date.label'))
			->add('endDate', 'oro_date', array('required' => false, 'label' => 'dmkclub.member.memberfeediscount.end_date.label'))
		;

		}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options){
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount',
                'cascade_validation' => true,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_member_memberfeediscount';
    }
}
