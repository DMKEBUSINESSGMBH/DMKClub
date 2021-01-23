<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Contracts\Translation\TranslatorInterface;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MemberFeeType extends AbstractType
{

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
	protected function buildPlainFields(FormBuilderInterface $builder, array $options)
	{

	    $builder->add('payedTotal', MoneyType::class, [
	        'required' => false,
	        'divisor' => 100,
	        'label' => 'dmkclub.member.memberfee.payed_total.label'
	    ]);

	    $builder->add('payedFull', CheckboxType::class, [
	        'required' => false,
	        'mapped' => false,
	        'label' => 'dmkclub.member.memberfee.payed_full.label'
	    ]);

	}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => MemberFee::class,
                'cascade_validation' => true,
            )
        );
    }
}
