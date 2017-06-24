<?php
namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use OroCRM\Bundle\ChannelBundle\Provider\RequestChannelProvider;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use OroCRM\Bundle\ContactBundle\Entity\Contact;
use OroCRM\Bundle\ContactBundle\Entity\ContactEmail;
use OroCRM\Bundle\ContactBundle\Entity\ContactPhone;
use Oro\Bundle\AddressBundle\Entity\Address;
use DMKClub\Bundle\PaymentBundle\Entity\BankAccount;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use DMKClub\Bundle\MemberBundle\Model\MemberStatus;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;

class CreateMemberByProposalHandler
{

    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var ObjectManager */
    protected $manager;

    /**
     * @var MemberManager
     */
    protected $memberManager;

    /**
     *
     * @param FormInterface $form
     * @param Request $request
     * @param ObjectManager $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $manager,
        MemberManager $memberManager
    )
    {
        $this->form = $form;
        $this->request = $request;
        $this->manager = $manager;
        $this->memberManager = $memberManager;
    }

    /**
     * Process form
     *
     * @param MemberProposal $entity
     *
     * @return mixed Array on successful processing, false otherwise
     */
    public function process(MemberProposal $entity)
    {
        $this->form->setData([
            'memberCode' => $this->memberManager->nextMemberCode(),
        ]);
        if (in_array($this->request->getMethod(), array(
            'POST',
            'PUT'
        ))) {
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
    protected function onSuccess(MemberProposal $entity)
    {
        $formData = $this->form->getData();
        $member = $this->memberManager->buildMemberByProposal($entity);
        $entity->setMember($member);
        $member->setStartDate($formData['startDate']);
        $member->setMemberCode($formData['memberCode']);
        $member->getContact()->setGender($formData['gender']);
        $entity->setStatus($this->getEnumValue('memberproposal_status', 'joined'));
        // Info an den Manager übergeben
        $this->manager->persist($member);
        $this->manager->persist($entity);
        $this->manager->flush();
        return true;
    }

    protected function getEnumValue($enumName, $valueId)
    {
        $className = ExtendHelper::buildEnumValueClassName($enumName);
        /** @var EnumValueRepository $enumRepo */
        $enumRepo = $this->manager->getRepository($className);
        return $enumRepo->findOneById($valueId);
    }

}
