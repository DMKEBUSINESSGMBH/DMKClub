<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MemberBillingType extends AbstractType {

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
			->add('name', 'text', array('required' => true, 'label' => 'dmkclub.member.memberbilling.name.label'))
			->add('startDate', 'oro_date', array('required' => false, 'label' => 'dmkclub.member.memberbilling.start_date.label'))
			->add('endDate', 'oro_date', array('required' => false, 'label' => 'dmkclub.member.memberbilling.end_date.label'))
			->add('owner')
			->add('organization')
		;

	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildRelationFields(FormBuilderInterface $builder, array $options){
	    // tags
	    $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));

	}
	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	    $resolver->setDefaults(
	        array(
	            'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberBilling',
	            'cascade_validation' => true,
	        )
	    );
	}

	/**
	 * @return string
	 */
	public function getName()
	{
	    return 'dmkclub_member_memberbilling';
	}
}
