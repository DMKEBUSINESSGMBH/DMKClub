<?php

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use Oro\Bundle\TagBundle\Entity\TagManager;

class SponsorHandler
{
	/** @var FormInterface */
	protected $form;

	/** @var Request */
	protected $request;

	/** @var ObjectManager */
	protected $manager;

	/**
	 * @param FormInterface          $form
	 * @param Request                $request
	 * @param ObjectManager          $manager
	 */
	public function __construct(
			FormInterface $form,
			Request $request,
			ObjectManager $manager
	) {
		$this->form                   = $form;
		$this->request                = $request;
		$this->manager                = $manager;
	}

	/**
	 * Process form
	 *
	 * @param  Lead $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process(Sponsor $entity)
	{

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
	 * @param Lead $entity
	 */
	protected function onSuccess(Sponsor $entity)
	{
		$this->manager->persist($entity);
		$this->manager->flush();
		$this->tagManager->saveTagging($entity);
	}
	/**
	 * Setter for tag manager
	 *
	 * @param TagManager $tagManager
	 */
	public function setTagManager(TagManager $tagManager)
	{
		$this->tagManager = $tagManager;
	}
}
