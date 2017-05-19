<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

class CreateBillsType extends AbstractType {

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
    	    ->add('billDate', 'oro_date', array('required' => true, 'label' => 'dmkclub.member.memberfee.bill_date.label'))
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
	    return 'dmkclub_member_createbills';
	}
}
