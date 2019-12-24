<?php

namespace DMKClub\Bundle\SponsorBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\SponsorBundle\Entity\Category;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryHandler
{
	/** @var FormInterface */
	protected $form;

	/** @var RequestStack */
	protected $request;

	/** @var ObjectManager */
	protected $manager;

	/**
	 * @param FormInterface          $form
	 * @param RequestStack           $request
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
	 * @param  Category $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process(Category $entity)
	{

		$this->form->setData($entity);

		$request = $this->request->getCurrentRequest();
		if (in_array($request->getMethod(), ['POST', 'PUT'])) {
			$this->form->submit($request);

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
	 * @param Category $entity
	 */
	protected function onSuccess(Category $entity)
	{
		$this->manager->persist($entity);
		$this->manager->flush();
	}
}
