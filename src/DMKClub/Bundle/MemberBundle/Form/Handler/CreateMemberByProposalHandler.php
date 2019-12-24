<?php
namespace DMKClub\Bundle\MemberBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\ChannelBundle\Provider\RequestChannelProvider;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;
use Oro\Bundle\FormBundle\Form\Handler\FormHandlerInterface;

class CreateMemberByProposalHandler implements FormHandlerInterface
{

    /** @var ObjectManager */
    protected $manager;

    /**
     * @var MemberManager
     */
    protected $memberManager;

    /**
     *
     * @param ObjectManager $manager
     * @param RequestChannelProvider $requestChannelProvider
     */
    public function __construct(
        ObjectManager $manager,
        MemberManager $memberManager
    )
    {
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
    public function process($entity, FormInterface $form, Request $request)
    {
        $form->setData([
            'memberCode' => $this->memberManager->nextMemberCode(),
        ]);
        if (in_array($request->getMethod(), [
            'POST',
            'PUT'
        ])) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                return $this->onSuccess($entity, $form);
            }
        }

        return false;
    }

    /**
     * "Success" form handler
     *
     * @param MemberProposal $entity
     */
    protected function onSuccess(MemberProposal $entity, FormInterface $form)
    {
        $formData = $form->getData();
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
