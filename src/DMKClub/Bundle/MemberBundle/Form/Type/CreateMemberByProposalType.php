<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use Oro\Bundle\UserBundle\Form\Type\GenderType;

class CreateMemberByProposalType extends AbstractType {

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
    	    ->add('memberCode', TextType::class, [
    	        'required' => true,
    	        'label' => 'dmkclub.member.member_code.label',
    	        'constraints' => [
    	            new Assert\NotNull([
    	                'message' => 'This is a required field.'
    	            ]),
    	        ],
    	    ])
    	    ->add('startDate', OroDateType::class, [
    	        'required' => true,
    	        'label' => 'dmkclub.member.start_date.label',
    	        'constraints' => [
    	            new Assert\NotNull([
    	                'message' => 'This is a required field.'
    	            ])
    	        ],
    	    ])
    	    ->add('gender', GenderType::class, [
    	        'required' => false,
    	        'label' => 'oro.contact.gender.label',
    	        'constraints' => [
    	            new Assert\NotNull([
    	               'message' => 'This is a required field.'
                    ])
    	        ],
    	    ])
    	    ;
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(
	        array(
	            'cascade_validation' => true,
	        )
	    );
	}
}
