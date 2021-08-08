<?php

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;

use Doctrine\Persistence\ObjectManager;
use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use Oro\Bundle\TagBundle\Entity\TagManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class SponsorHandler implements FormHandlerInterface
{
	/** @var ObjectManager */
	protected $manager;

	/**
	 * @param FormInterface          $form
	 * @param RequestStack           $request
	 * @param ObjectManager          $manager
	 */
	public function __construct(
			ObjectManager $manager
	) {
		$this->manager                = $manager;
	}

	/**
	 * Process form
	 *
	 * @param  Sponsor $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process($entity, FormInterface $form, Request $request)
	{

		$form->setData($entity);

		if (in_array($request->getMethod(), ['POST', 'PUT'])) {
			$form->handleRequest($request);

			if ($form->isValid()) {
				$this->onSuccess($entity);

				return true;
			}
		}

		return false;
	}

	/**
	 * "Success" form handler
	 *
	 * @param Sponsor $entity
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
