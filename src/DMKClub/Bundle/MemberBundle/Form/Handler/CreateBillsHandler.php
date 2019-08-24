<?php
namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Oro\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;

class CreateBillsHandler
{

    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /* @var \DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager */
    protected $memberBillingManager;

    /**
     *
     * @param FormInterface $form
     * @param Request $request
     * @param ObjectManager $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(FormInterface $form, Request $request, MemberBillingManager $memberBillingManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->memberBillingManager = $memberBillingManager;
    }

    /**
     * Process form
     *
     * @param MemberBilling $entity
     *
     * @return mixed Array on successful processing, false otherwise
     */
    public function process(MemberBilling $entity)
    {
        $this->form->setData([]);

        if (in_array($this->request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $this->form->submit($this->request);
            if ($this->form->isValid()) {
                return $this->onSuccess($entity);
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param MemberBilling $entity
     */
    protected function onSuccess(MemberBilling $entity)
    {
        // Info an den Manager übergeben
        $formData = $this->form->getData();
        $formData['async'] = true;
        return $this->memberBillingManager->startAccounting($entity, $formData);
    }
}
