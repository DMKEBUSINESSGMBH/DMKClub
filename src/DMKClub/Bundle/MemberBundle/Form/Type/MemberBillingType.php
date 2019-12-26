<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\FormBundle\Form\Type\OroDateType;

use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use DMKClub\Bundle\PaymentBundle\Form\Type\SepaCreditorSelectType;
use DMKClub\Bundle\BasicsBundle\Form\Type\TwigTemplateSelectType;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use Symfony\Component\VarDumper\VarDumper;

class MemberBillingType extends AbstractType
{

    /**
     *
     * @var EventSubscriberInterface[]
     */
    protected $subscribers = [];

    /** @var TranslatorInterface */
    protected $translator;

    /**
     *
     * @var ProcessorProvider
     */
    protected $processorProvider;

    /**
     *
     * @var array
     */
    protected $fileSystemMap = NULL;

    /**
     *
     * @param TranslatorInterface $translator
     * @param ProcessorProvider $processorProvider
     * @param FilesystemMap $fileSystemMap
     */
    public function __construct(TranslatorInterface $translator, ProcessorProvider $processorProvider, FilesystemMap $fileSystemMap)
    {
        $this->translator = $translator;
        $this->processorProvider = $processorProvider;
        $this->fileSystemMap = (array) $fileSystemMap;
    }

    /**
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->subscribers[] = $subscriber;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
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
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'required' => true,
                'label' => 'dmkclub.member.memberbilling.name.label'
            ])
            ->add('startDate', OroDateType::class, [
                'required' => true,
                'label' => 'dmkclub.member.memberbilling.start_date.label'
            ])
            ->add('endDate', OroDateType::class, [
                'required' => true,
                'label' => 'dmkclub.member.memberbilling.end_date.label'
            ])
            ->add('positionLabels', TextareaType::class, [
                'required' => true,
                'label' => 'dmkclub.member.memberbilling.position_labels.label',
                'tooltip' => 'dmkclub.member.memberbilling.position_labels.tooltip'
            ])
            ->add('exportFilesystem', ChoiceType::class, [
                'required' => false,
                'label' => 'dmkclub.member.memberbilling.export_filesystem.label',
                'choices' => $this->getFilesystems(),
                'placeholder' => 'dmkclub.form.choose',
                'tooltip' => 'dmkclub.member.memberbilling.export_filesystem.tooltip'
            ]);
    }

    protected function getFilesystems()
    {
        $options = [];
        $fsm = reset($this->fileSystemMap);
        foreach ($fsm as $fsName => $filesystem) {
            /* @var $filesystem \Gaufrette\Filesystem */
            if ($fsName == 'attachments') {
                continue; // skip oro attachment fs
            }
            $clazz = explode('\\', get_class($filesystem->getAdapter()));
            $fsType = array_pop($clazz);
            $adapterData = (array) $filesystem->getAdapter();
            $dirName = '';
            foreach ($adapterData as $key => $value) {
                if (strstr($key, 'directory') !== false) {
                    $dirName = $value;
                    break;
                }
            }
            $options[sprintf('%s - %s (%s)', $fsType, $fsName, $dirName)] = $fsName;
        }
        return $options;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
        // tags removed in 1.9
        // $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));

        // Add Member-List Segment
        $builder->add('segment',
            MemberSegmentSelectType::class,
            [
                'label' => 'dmkclub.member.memberbilling.segment.label',
                'required' => false,
                'entities' => [
                    'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
                ]
        ]);

        $builder->add('sepaCreditor',
            SepaCreditorSelectType::class,
            [
                'label' => 'dmkclub.member.memberbilling.sepa_creditor.label',
                'required' => false
        ]);

        $builder->add('template',
            TwigTemplateSelectType::class,
            [
                'label' => 'dmkclub.member.memberbilling.template.label',
                'required' => false,
                'tooltip' => 'dmkclub.member.memberbilling.template.tooltip'
            ]
        );

        // Einstellungsformular für dem Processor
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
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
                if ($currentProcessorName && ! array_key_exists($currentProcessorName, $choices)) {
                    $currentProcessor = $this->processorProvider->getProcessorByName($currentProcessorName);
                    $choices[$currentProcessor->getName()] = $currentProcessor->getLabel();
                }
                $options['choices'] = array_flip($choices);
            }

            $form = $event->getForm();
            $form->add('processor', ProcessorSelectType::class, $options);
        });
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberBilling',
            'cascade_validation' => true
        ]);
    }
}
