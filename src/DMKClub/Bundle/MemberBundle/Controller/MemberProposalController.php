<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposalAddress;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberProposalHandler;
use DMKClub\Bundle\MemberBundle\Form\Handler\CreateMemberByProposalHandler;

/**
 * @Route("/member/proposal")
 */
class MemberProposalController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            MemberProposalHandler::class,
            CreateMemberByProposalHandler::class,
            'dmkclub_member.memberproposal.form' => Form::class,
            'dmkclub_member.memberproposal.createmember.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }

    /**
     * @Route("/", name="dmkclub_member_proposal_index")
     * @AclAncestor("dmkclub_member_proposal_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => MemberProposal::class,
        ];
    }
    /**
     * @Route("/create", name="dmkclub_member_proposal_create")
     * @Template("DMKClubMemberBundle:MemberProposal:update.html.twig")
     * @Acl(
     *      id="dmkclub_member_proposal_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubMemberBundle:MemberProposal"
     * )
     */
    public function createAction()
    {
        $address = new MemberProposalAddress();
        $address->setCountry($this->getDefaultCountry());
        $address->setRegion($this->getDefaultRegion());
        $proposal = new MemberProposal();
        $proposal->setPostalAddress($address);
    	return $this->update($proposal);
    }
    protected function getDefaultCountry()
    {
        return $this->getDoctrine()->getRepository('OroAddressBundle:Country')->findOneBy(['iso2Code' => 'DE']);
    }
    protected function getDefaultRegion()
    {
        return $this->getDoctrine()->getRepository('OroAddressBundle:Region')->findOneBy(['combinedCode' => 'DE-SN']);
    }
    /**
     * @Route("/update/{id}", name="dmkclub_member_proposal_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_member_proposal_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubMemberBundle:MemberProposal"
     * )
     */
    public function updateAction(MemberProposal $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param MemberProposal $entity
     *
     * @return array
     */
    protected function update(MemberProposal $entity)
    {
    	/* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandlerFacade */
        $handler = $this->get(UpdateHandlerFacade::class);
    	$data = $handler->update(
    	    $entity,
			$this->get('dmkclub_member.memberproposal.form'),
			$this->get(TranslatorInterface::class)->trans('dmkclub.member.memberproposal.message.saved'),
    	    null,
			$this->get(MemberProposalHandler::class)
		);
    	return $data;
    }

    /**
     * @Route("/view/{id}", name="dmkclub_member_proposal_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_member_proposal_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubMemberBundle:MemberProposal"
     * )
     * @Template
     */
    public function viewAction(MemberProposal $member)
    {
        return ['entity' => $member];
    }

    /**
     * @Route("/widget/info/{id}", name="dmkclub_member_proposal_widget_info", requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_member_proposal_view")
     * @Template
     */
    public function infoAction(MemberProposal $member)
    {
        return ['entity' => $member];
    }
    /**
     * @Route("/widget/additionalinfo/{id}", name="dmkclub_member_proposal_widget_additionalinfo", requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_member_proposal_view")
     * @Template
     */
    public function additionalInfoAction(MemberProposal $member)
    {
        return ['entity' => $member];
    }

    /**
     * @Route("/widget/discountinfo/{id}", name="dmkclub_member_proposal_widget_discountinfo", requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_member_proposal_view")
     * @Template
     */
    public function discountInfoAction(MemberProposal $member)
    {
        return ['entity' => $member];
    }

    /**
     * Create member by proposal
     * @Route("/{id}/createmember", name="dmkclub_member_proposal_createmember", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("dmkclub_member_create")
     */
    public function createMemberAction(MemberProposal $entity)
    {
        $form = $this->getCreateMemberForm();
        $response = [
            'entity' => $entity,
            'saved' => false,
        ];

        $request = $this->get('request_stack')->getCurrentRequest();
        // Form auswerten
        if ($this->get(CreateMemberByProposalHandler::class)->process($entity, $form, $request)) {
            $response['message'] = 'finished';
            $response['saved'] = true;
        }
        $response['form'] = $form->createView();

        return $response;
    }
    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function getCreateMemberForm()
    {
        return $this->get('dmkclub_member.memberproposal.createmember.form');
    }

}
