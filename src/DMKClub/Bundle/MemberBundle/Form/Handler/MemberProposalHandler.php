<?php

namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\PaymentBundle\Sepa\Iban\OpenIBAN;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;

class MemberProposalHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
    protected $request;

    /** @var EntityManager */
    protected $em;

    protected $openIban;
    protected $logger;

    /**
     * @param FormInterface $form
     * @param RequestStack       $request
     * @param EntityManager $em
     */
    public function __construct(FormInterface $form, RequestStack $request, EntityManager $em, OpenIBAN $openIban, Logger $logger)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->em       = $em;
        $this->openIban = $openIban;
        $this->logger   = $logger;
    }

    /**
     * Process form
     *
     * @param MemberProposal $entity
     *
     * @return bool  True on successful processing, false otherwise
     */
    public function process(MemberProposal $entity)
    {
        $this->getForm()->setData($entity);

        $request = $this->request->getCurrentRequest();
        if (in_array($request->getMethod(), ['POST', 'PUT'])) {
            $this->getForm()->submit($request);

            if ($this->getForm()->isValid()) {

                $this->onSuccess($entity);

                return true;
            }
        }

        return false;
    }
    protected function onSuccess(MemberProposal $entity)
    {
        $bankAccount = $entity->getBankAccount();
        if($bankAccount && $bankAccount->getIban() && !$bankAccount->getBic()) {
            try {
                $bankData = $this->openIban->lookupBic($bankAccount->getIban());
                if($bankData) {
                    $bankAccount->setBic($bankData->bic);
                    if(!$bankAccount->getBankName()) {
                        $bankAccount->setBankName($bankData->name);
                    }
                }
            }
            catch(\Exception $e) {
                $this->logger->error('IBAN validation failed', ['e' => $e]);
            }
        }
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
