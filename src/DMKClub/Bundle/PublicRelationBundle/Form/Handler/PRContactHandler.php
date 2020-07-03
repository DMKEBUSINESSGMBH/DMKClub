<?php

namespace DMKClub\Bundle\PublicRelationBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\PublicRelationBundle\Entity\PRContact;
use Symfony\Component\HttpFoundation\RequestStack;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;

class PRContactHandler implements FormHandlerInterface
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
     * @param  PRContact $entity
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
     * @param PRContact $entity
     */
    protected function onSuccess(PRContact $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
