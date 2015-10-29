<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeeCategoryType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildPlainFields($builder, $options);
	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	protected function buildPlainFields(FormBuilderInterface $builder, array $options) {
		$builder
		->add('name', 'text', array('required' => true, 'label' => 'dmkclub.member.feecategory.name.label'))
		->add('owner')
		->add('organization')
		;
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(
				array(
						'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\FeeCategory',
						'cascade_validation' => true,
				)
		);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'dmkclub_member_feecategory';
	}
}
