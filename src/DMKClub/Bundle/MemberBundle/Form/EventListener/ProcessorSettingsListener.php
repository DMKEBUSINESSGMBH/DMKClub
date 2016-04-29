<?php

namespace DMKClub\Bundle\MemberBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Accounting\ProcessorInterface;

class ProcessorSettingsListener implements EventSubscriberInterface
{
    /**
     * @var ProcessorProvider
     */
    protected $processorProvider;

    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @param ProcessorProvider $processorProvider
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(ProcessorProvider $processorProvider, DoctrineHelper $doctrineHelper)
    {
        $this->processorProvider = $processorProvider;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA  => 'preSet',
            FormEvents::POST_SET_DATA => 'postSet',
            FormEvents::PRE_SUBMIT    => 'preSubmit'
        ];
    }

    /**
     * Add Processor Settings form if any for existing entities.
     *
     * @param FormEvent $event
     */
    public function preSet(FormEvent $event)
    {
        /** @var MemberBilling $data */
        $data = $event->getData();
        if ($data === null) {
            return;
        }

        $selectedProcessor = $this->getSelectedProcessor($data->getProcessor());
        if ($selectedProcessor) {
            $this->addProcessorSettingsForm($selectedProcessor, $event->getForm());
            $data->setProcessor($selectedProcessor->getName());
        }
        $event->setData($data);
    }

    /**
     * Set correct processor setting value.
     *
     * @param FormEvent $event
     */
    public function postSet(FormEvent $event)
    {
        /** @var MemberBilling $data */
        $data = $event->getData();

        if ($data === null) {
            return;
        }

        $form = $event->getForm();
        $form->get('processor')->setData($data->getProcessor());
    }

    /**
     * Change processor settings subform to form matching processor passed in request.
     * Pass top level data to processorSettings.
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $formData = $form->getData();

        $processorName = isset($data['processor']) ? $data['processor'] : '';

        $selectedProcessor = $this->getSelectedProcessor($processorName);
        if ($selectedProcessor->getName() != $formData->getProcessor()) {
        	$config = $formData->getProcessorConfig();
        	$config = $config ? unserialize($config) : [];
        	$newSettings = isset($config[$processorName]) ? $config[$processorName] : [];
//         	print_r(['data'=>\Doctrine\Common\Util\Debug::dump($data), 'formdata' => \Doctrine\Common\Util\Debug::dump($formData)]);
//         	exit();


          $formData->setProcessorSettings($newSettings);
        }

        if ($selectedProcessor) {
            $this->addProcessorSettingsForm($selectedProcessor, $form);
            $formData->setProcessor($selectedProcessor->getName());
            $form->get('processor')->setData($selectedProcessor->getName());
        }

        if ($form->has('processorSettings')) {
            $parentData = $data;
            unset($parentData['processorSettings']);
            $data['processorSettings']['parentData'] = $parentData;
        }

        $event->setData($data);
    }

    /**
     * @param ProcessorInterface $selectedProcessor
     * @param FormInterface $form
     */
    protected function addProcessorSettingsForm(ProcessorInterface $selectedProcessor, FormInterface $form)
    {
        if ($selectedProcessor) {
            $processorSettingsFormType = $selectedProcessor->getSettingsFormType();
            if ($processorSettingsFormType) {
                $form->add('processorSettings', $processorSettingsFormType, ['required' => true]);
            } elseif ($form->has('processorSettings')) {
                $form->remove('processorSettings');
            }
        }
    }

    /**
     * @param string $selectedProcessorName
     * @return ProcessorInterface
     */
    protected function getSelectedProcessor($selectedProcessorName)
    {
        if ($selectedProcessorName) {
            $selectedProcessor = $this->processorProvider->getProcessorByName($selectedProcessorName);
        } else {
            $processorChoices = $this->processorProvider->getProcessors();
            $selectedProcessor = reset($processorChoices);
        }

        return $selectedProcessor;
    }
}
