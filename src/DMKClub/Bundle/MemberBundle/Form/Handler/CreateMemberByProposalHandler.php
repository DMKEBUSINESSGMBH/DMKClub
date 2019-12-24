<?php
namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\ChannelBundle\Provider\RequestChannelProvider;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;
use Symfony\Component\HttpFoundation\RequestStack;

class CreateMemberByProposalHandler
{

    /** @var FormInterface */
    protected $form;

    /** @var RequestStack */
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
     * @param RequestStack $request
     * @param ObjectManager $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(
        FormInterface $form,
        RequestStack $request,
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
        $request = $this->request->getCurrentRequest();
        if (in_array($request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $this->form->submit($request);
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
