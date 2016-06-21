<?php

namespace DMKClub\Bundle\PaymentBundle\Form\Handler;

use OroCRM\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use DMKClub\Bundle\SponsorBundle\Entity\Category;
use DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor;

class SepaCreditorHandler {
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
	 * @param  Category $entity
	 *
	 * @return bool True on successful processing, false otherwise
	 */
	public function process(SepaCreditor $entity)
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
	 * @param Category $entity
	 */
	protected function onSuccess(SepaCreditor $entity)
	{
		$this->manager->persist($entity);
		$this->manager->flush();
	}
}
