<?php
namespace DMKClub\Bundle\BasicsBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Persistence\ObjectManager;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;

class TwigTemplateHandler
{

    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
    protected $request;

    /** @var ObjectManager */
    protected $manager;

    /**
     *
     * @param FormInterface $form
     * @param RequestStack $request
     * @param ObjectManager $manager
     */
    public function __construct(FormInterface $form, RequestStack $request, ObjectManager $manager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->manager = $manager;
    }

    /**
     * Process form
     *
     * @param TwigTemplate $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(TwigTemplate $entity)
    {
        $this->form->setData($entity);

        $request = $this->request->getCurrentRequest();
        if (in_array($request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $this->form->handleRequest($request);

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
     * @param TwigTemplate $entity
     */
    protected function onSuccess(TwigTemplate $entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
