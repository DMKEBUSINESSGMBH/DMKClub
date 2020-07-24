<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposalAddress;

/**
 * @Route("/member/proposal")
 */
class MemberProposalController extends AbstractController
{
    /**
     * @Route("/", name="dmkclub_member_proposal_index")
     * @AclAncestor("dmkclub_member_proposal_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('dmkclub_member.memberproposal.entity.class')
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
    	$handler = $this->get('oro_form.update_handler');
    	$data = $handler->update(
    	    $entity,
			$this->get('dmkclub_member.memberproposal.form'),
			$this->get('translator')->trans('dmkclub.member.memberproposal.message.saved'),
    	    null,
			$this->get('dmkclub_member.memberproposal.form.handler')
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

        $request = $this->container->get('request_stack')->getCurrentRequest();
        // Form auswerten
        if ($this->get('dmkclub_member.memberproposal.createmember.form.handler')->process($entity, $form, $request)) {
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
