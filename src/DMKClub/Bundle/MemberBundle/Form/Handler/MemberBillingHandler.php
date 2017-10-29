<?php

namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\TagBundle\Entity\TagManager;
use Oro\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;

class MemberBillingHandler
{
	/** @var FormInterface */
	protected $form;

	/** @var Request */
	protected $request;

	/** @var ObjectManager */
	protected $manager;

	/* @var \DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager */
	protected $memberBillingManager;
	/**
	 * @param FormInterface          $form
	 * @param Request                $request
	 * @param ObjectManager          $manager
	 * @param RequestChannelProvider $requestChannelProvider
	 */
	public function __construct(FormInterface $form, Request $request, ObjectManager $manager,
			MemberBillingManager $memberBillingManager) {
	    $this->form              = $form;
	    $this->request           = $request;
	    $this->manager           = $manager;
	    $this->memberBillingManager = $memberBillingManager;
	}

	/**
	 * Process form
	 *
	 * @param  MemberBilling $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process(MemberBilling $entity)
	{
		$this->restoreProcessorSettings($entity);

		$this->form->setData($entity);

		if (in_array($this->request->getMethod(), array('POST', 'PUT'))) {
		    $this->form->submit($this->request);

		    if ($this->form->isValid()) {
		        $this->onSuccess($entity);

		        return true;
		    }
		}

		return false;
	}

	/**
	 * "Success" form handler
	 *
	 * @param MemberBilling $entity
	 */
	protected function onSuccess(MemberBilling $entity) {
		// TODO: Hier die Daten aus dem VO serialisieren!
		$this->saveProcessorConfig($entity);
		$this->manager->persist($entity);
		$this->manager->flush();
		$this->tagManager->saveTagging($entity);
	}
	protected function saveProcessorConfig(MemberBilling $entity) {
		// Die alte, serialisierte Config für alle Prozessoren holen
		$configData = $entity->getProcessorConfig();
		$configData = $configData ? unserialize($configData) : [];
		// Die Daten für den aktuellen Prozessor neu schreiben
		$configData[$entity->getProcessor()] = $entity->getProcessorSettings();

		$entity->setProcessorConfig(serialize($configData));

	}

	/**
	 *
	 * @param MemberBilling $entity
	 */
	protected function restoreProcessorSettings(MemberBilling $entity) {
		// Hier müssen wir eingreifen. Die Storedaten sind serialisiert in der
		// processorConfig drin. Sie müssen in ein VO überführt und dann in
		// processorSetting gesetzt werden.
		// Beim Wechsel des processortypes muss man aber aufpassen, damit die Config noch passt!
		$entity->setProcessorSettings($this->memberBillingManager->getProcessorSettings($entity));
	}
	/**
	 * Setter for tag manager
	 *
	 * @param TagManager $tagManager
	 */
	public function setTagManager(TagManager $tagManager) {
		$this->tagManager = $tagManager;
	}
}
