<?php
namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Oro\Bundle\ChannelBundle\Provider\RequestChannelProvider;

use Symfony\Component\Form\FormInterface;

use Doctrine\Common\Persistence\ObjectManager;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use Symfony\Component\HttpFoundation\RequestStack;

class CreateBillsHandler
{

    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
    protected $request;

    /* @var \DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager */
    protected $memberBillingManager;

    /**
     *
     * @param FormInterface $form
     * @param RequestStack $request
     * @param ObjectManager $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(FormInterface $form, RequestStack $request, MemberBillingManager $memberBillingManager)
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

        $request = $this->request->getCurrentRequest();
        if (in_array($request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $this->form->handleRequest($request);
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
