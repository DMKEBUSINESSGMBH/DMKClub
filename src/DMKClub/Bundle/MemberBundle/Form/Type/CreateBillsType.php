<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;

class CreateBillsType extends AbstractType {

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
	    $builder
    	    ->add('billDate', OroDateType::class, [
    	        'required' => true,
    	        'label' => 'dmkclub.member.memberfee.bill_date.label']
    	    );
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(
	        [
	            'cascade_validation' => true,
	        ]
	    );
	}
}
