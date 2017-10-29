<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMemberByProposalType extends AbstractType {

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
	    $builder
    	    ->add('memberCode', 'text', [
    	        'required' => true,
    	        'label' => 'dmkclub.member.member_code.label',
    	        'constraints' => [
    	            new Assert\NotNull([
    	                'message' => 'This is a required field.'
    	            ]),
    	        ],
    	    ])
    	    ->add('startDate', 'oro_date', [
    	        'required' => true,
    	        'label' => 'dmkclub.member.start_date.label',
    	        'constraints' => [
    	            new Assert\NotNull([
    	                'message' => 'This is a required field.'
    	            ])
    	        ],
    	    ])
    	    ->add('gender', 'oro_gender', [
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
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(
	        array(
	            'cascade_validation' => true,
	        )
	    );
	}

	/**
	 * @return string
	 */
	public function getName()
	{
	    return 'dmkclub_member_creatememberbyproposal';
	}
}
