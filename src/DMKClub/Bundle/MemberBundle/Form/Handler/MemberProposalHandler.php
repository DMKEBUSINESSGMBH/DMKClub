<?php

namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\PaymentBundle\Sepa\Iban\OpenIBAN;
use Monolog\Logger;

class MemberProposalHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var EntityManager */
    protected $em;

    protected $openIban;
    protected $logger;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param EntityManager $em
     */
    public function __construct(FormInterface $form, Request $request, EntityManager $em, OpenIBAN $openIban, Logger $logger)
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

        if (in_array($this->request->getMethod(), array('POST', 'PUT'))) {
            $this->getForm()->submit($this->request);

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
