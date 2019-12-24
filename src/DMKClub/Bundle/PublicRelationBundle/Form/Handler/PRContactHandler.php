<?php

namespace DMKClub\Bundle\PublicRelationBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\PublicRelationBundle\Entity\PRContact;
use Symfony\Component\HttpFoundation\RequestStack;

class PRContactHandler
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
        RequestStack $request,
        ObjectManager $manager
    ) {
        $this->form                   = $form;
        $this->request                = $request;
        $this->manager                = $manager;
    }

    /**
     * Process form
     *
     * @param  PRContact $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(PRContact $entity)
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
     * @param PRContact $entity
     */
    protected function onSuccess(PRContact $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
