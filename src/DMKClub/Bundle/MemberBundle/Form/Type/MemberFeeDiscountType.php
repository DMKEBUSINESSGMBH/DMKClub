<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberFeeDiscountType extends AbstractType {

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
			->add('reason', TextType::class, array('required' => true, 'label' => 'dmkclub.member.memberfeediscount.reason.label'))
			->add('startDate', OroDateType::class, array('required' => true, 'label' => 'dmkclub.member.memberfeediscount.start_date.label'))
			->add('endDate', OroDateType::class, array('required' => false, 'label' => 'dmkclub.member.memberfeediscount.end_date.label'))
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount',
                'cascade_validation' => true,
            )
        );
    }

}
