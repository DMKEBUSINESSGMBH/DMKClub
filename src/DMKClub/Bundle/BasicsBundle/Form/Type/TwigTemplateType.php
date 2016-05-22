<?php

namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class TwigTemplateType extends AbstractType {

	/** @var TranslatorInterface */
	protected $translator;

	/**
	 * @param ConfigManager       $configManager
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator) {
		$this->translator = $translator;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$this->buildPlainFields($builder, $options);
		$this->buildRelationFields($builder, $options);
	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	protected function buildPlainFields(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array('required' => true, 'label' => 'dmkclub.basics.twigtemplate.name.label'))
			->add('template', 'oro_rich_text', array(
					'required' => true,
					'label' => 'dmkclub.basics.twigtemplate.template.label',
					'attr'            => [
						'class'                => 'template-editor',
						'data-wysiwyg-enabled' => true,
					],
					'wysiwyg_options' => [
						'height'     => '250px'
					]
				)
			)
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
	}
	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	    $resolver->setDefaults(
	        array(
	            'data_class' => 'DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate',
	            'cascade_validation' => true,
	        )
	    );
	}

	/**
	 * @return string
	 */
	public function getName() {
	    return 'dmkclub_basics_twigtemplate';
	}
}
