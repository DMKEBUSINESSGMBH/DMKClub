<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use OroCRM\Bundle\AccountBundle\Entity\Account;
use OroCRM\Bundle\ChannelBundle\Entity\Channel;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/memberbilling")
 */
class MemberBillingController extends Controller
{
	/**
	 * @Route("/", name="dmkclub_memberbilling_index")
	 * @AclAncestor("dmkclub_memberbilling_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
		    'entity_class' => $this->container->getParameter('dmkclub_member.memberbilling.entity.class')
		];
	}
	/**
	 * Create member form
	 * @Route("/create", name="dmkclub_memberbilling_create")
	 * @Template("DMKClubMemberBundle:MemberBilling:update.html.twig")
	 * @Acl(
	 *      id="dmkclub_memberbilling_create",
	 *      type="entity",
	 *      permission="CREATE",
	 *      class="DMKClubMemberBundle:MemberBilling"
	 * )
	 */
	public function createAction()
	{
		return $this->update(new MemberBilling());
	}
	/**
	 * Update memberbilling form
	 * @Route("/update/{id}", name="dmkclub_memberbilling_update", requirements={"id"="\d+"}, defaults={"id"=0})
	 *
	 * @Template
	 * @Acl(
	 *      id="dmkclub_memberbilling_update",
	 *      type="entity",
	 *      permission="EDIT",
	 *      class="DMKClubMemberBundle:MemberBilling"
	 * )
	 */
	public function updateAction(MemberBilling $entity)
	{
		return $this->update($entity);
	}
	/**
	 * @param MemberBilling $entity
	 *
	 * @return array
	 */
	protected function update(MemberBilling $entity)
	{
		return $this->get('oro_form.model.update_handler')->handleUpdate(
				$entity,
				$this->get('dmkclub_member.memberbilling.form'),
				function (MemberBilling $entity) {
					return array(
							'route' => 'dmkclub_memberbilling_update',
							'parameters' => array('id' => $entity->getId())
					);
				},
				function (MemberBilling $entity) {
					return array(
							'route' => 'dmkclub_memberbilling_view',
							'parameters' => array('id' => $entity->getId())
					);
				},
				$this->get('translator')->trans('dmkclub.member.memberbilling.message.saved'),
				$this->get('dmkclub_member.memberbilling.form.handler')
		);
	}

	/**
	 * @Route("/view/{id}", name="dmkclub_memberbilling_view", requirements={"id"="\d+"}))
	 * @Acl(
	 *      id="dmkclub_memberbilling_view",
	 *      type="entity",
	 *      permission="VIEW",
	 *      class="DMKClubMemberBundle:MemberBilling"
	 * )
	 * @Template
	 */
	public function viewAction(MemberBilling $entity)
	{
		return ['entity' => $entity];
	}

	/**
	 * Wird zur Anzeige des Info-Widgets in der Detailansicht verwendet
	 * @Route("/widget/info/{id}", name="dmkclub_memberbilling_widget_info", requirements={"id"="\d+"}))
	 * @AclAncestor("dmkclub_memberbilling_view")
	 * @Template
	 */
	public function infoAction(MemberBilling $entity)
	{
		return ['entity' => $entity];
	}
	/**
	 * @Route("/createbills/{id}", name="dmkclub_memberbilling_createbills", requirements={"id"="\d+"})
	 * @AclAncestor("dmkclub_memberbilling_create")
	 * @Template
	 */
	public function createBillsAction(MemberBilling $entity)
	{
		// Info an den Manager übergeben
		$ret = $this->get('dmkclub_member.memberbilling.manager')->startAccounting($entity);

		$this->get('session')
			->getFlashBag()
			->add('success', $this->get('translator')->trans('Auswertung gestartet: ' . $ret['success']));
		return new RedirectResponse(
				$this->generateUrl('dmkclub_memberbilling_view', ['id' => $entity->getId()]));
	}

}
