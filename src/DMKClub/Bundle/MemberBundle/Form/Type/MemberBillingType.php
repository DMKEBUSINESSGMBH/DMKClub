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

class MemberBillingType extends AbstractType {
	/**
	 * @var EventSubscriberInterface[]
	 */
	protected $subscribers = [];

	/** @var TranslatorInterface */
	protected $translator;
	/**
	 * @var ProcessorProvider
	 */
	protected $processorProvider;

	/**
	 * @var array
	 */
	protected $fileSystemMap = NULL;

	/**
	 * @param ConfigManager       $configManager
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator, ProcessorProvider $processorProvider, FilesystemMap $fileSystemMap)
	{
		$this->translator = $translator;
		$this->processorProvider = $processorProvider;
		$this->fileSystemMap = (array) $fileSystemMap;
	}
	/**
	 * @param EventSubscriberInterface $subscriber
	 */
	public function addSubscriber(EventSubscriberInterface $subscriber)
	{
		$this->subscribers[] = $subscriber;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		foreach ($this->subscribers as $subscriber) {
			$builder->addEventSubscriber($subscriber);
		}

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
			->add('startDate', 'oro_date', array('required' => true, 'label' => 'dmkclub.member.memberbilling.start_date.label'))
			->add('endDate', 'oro_date', array('required' => true, 'label' => 'dmkclub.member.memberbilling.end_date.label'))
			->add('positionLabels', 'textarea', array('required' => true, 'label' => 'dmkclub.member.memberbilling.position_labels.label'))

			->add('exportFilesystem', 'choice', array(
					'required' => false,
					'label' => 'dmkclub.member.memberbilling.export_filesystem.label',
					'choices' => $this->getFilesystems(),
					'empty_value' => 'dmkclub.form.choose',
				)
			)

			->add('owner')
			->add('organization')
		;
	}
	protected function getFilesystems() {
		$options = [];
		$fsm = reset($this->fileSystemMap);
		foreach ($fsm As $fsName => $filesystem) {
			/* @var $filesystem \Gaufrette\Filesystem */
			if($fsName == 'attachments')
				continue; // skip oro attachment fs

			$clazz = explode('\\', get_class($filesystem->getAdapter()));
			$fsType = array_pop($clazz);
			$adapterData = (array)$filesystem->getAdapter();
			$info = '';
			foreach($adapterData As $key => $value) {
				if(strstr($key, 'directory') !== false) {
					$info = ' ('.$value.')';
					break;
				}
			}
			$options[$fsName] = $fsType . $info;
		}
		return $options;
	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildRelationFields(FormBuilderInterface $builder, array $options){
		// tags removed in 1.9
		// $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));

		// Add Member-List Segment
		$builder->add(
				'segment',
				'dmkclub_member_segment_select_type',
				[
						'label' => 'oro.segment.entity_label',
						'required' => false,
						'entities' => [
								'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
						],
				]
		);

		$builder->add(
				'sepaCreditor',
				'dmkclub_sepacreditor_select',
				[
						'label' => 'dmkclub.member.memberbilling.sepa_creditor.label',
						'required' => false,
				]
		);

		$builder->add(
				'template',
				'dmkclub_basics_twigtemplate_select',
				[
						'label' => 'dmkclub.member.memberbilling.template.label',
						'required' => false,
				]
		);

		// Einstellungsformular für dem Processor
		$builder->addEventListener(
				FormEvents::PRE_SET_DATA,
				function (FormEvent $event) {
					$options = [
							'label' => 'dmkclub.member.memberbilling.processor.label',
							'required' => true,
							'mapped' => false
					];

					/** @var MemberBilling $data */
					$data = $event->getData();
					if ($data) {
						$choices = $this->processorProvider->getVisibleProcessorChoices();
						$currentProcessorName = $data->getProcessor();
						if ($currentProcessorName && !array_key_exists($currentProcessorName, $choices)) {
							$currentProcessor = $this->processorProvider->getProcessorByName($currentProcessorName);
							$choices[$currentProcessor->getName()] = $currentProcessor->getLabel();
							$options['choices'] = $choices;
						}
					}

					$form = $event->getForm();
					$form->add('processor', 'dmkclub_member_accounting_processor_select', $options);
				}
		);
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
