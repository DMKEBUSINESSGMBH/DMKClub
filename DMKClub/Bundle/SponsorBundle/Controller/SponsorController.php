<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\SponsorBundle\Entity\Sponsor;
use OroCRM\Bundle\AccountBundle\Entity\Account;
use OroCRM\Bundle\ChannelBundle\Entity\Channel;


/**
 * @Route("/sponsor")
 */
class SponsorController extends Controller {
	/**
	 * @Route("/", name="dmkclub_sponsor_index")
	 * @AclAncestor("dmkclub_sponsor_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => $this->container->getParameter('dmkclub.sponsor.entity.class')
		];
	}
    /**
     * Create sponsor form
     * @Route("/create", name="dmkclub_sponsor_create")
     * @Template("DMKClubSponsorBundle:Sponsor:update.html.twig")
     * @Acl(
     *      id="dmkclub_sponsor_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubSponsorBundle:Sponsor"
     * )
     */
    public function createAction() {
    	return $this->update(new Sponsor());
    }
    /**
     * Update sponsor form
     * @Route("/update/{id}", name="dmkclub_sponsor_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_sponsor_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubSponsorBundle:Sponsor"
     * )
     */
    public function updateAction(Sponsor $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param Sponsor $entity
     *
     * @return array
     */
    protected function update(Sponsor $entity)
    {
    	return $this->get('oro_form.model.update_handler')->handleUpdate(
    			$entity,
    			$this->get('dmkclub.sponsor.form'),
    			function (Sponsor $entity) {
    				return array(
    						'route' => 'dmkclub_sponsor_update',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			function (Sponsor $entity) {
    				return array(
    						'route' => 'dmkclub_sponsor_view',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			$this->get('translator')->trans('dmkclub.controller.sponsor.saved.message'),
    			$this->get('dmkclub.sponsor.form.handler')
    	);
    }
    /**
     * @Route("/view/{id}", name="dmkclub_sponsor_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_sponsor_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubSponsorBundle:Sponsor"
     * )
     * @Template
     */
    public function viewAction(Sponsor $entity) {
        return ['entity' => $entity];
    }
    /**
     * @Route("/widget/info/{id}", name="dmkclub_sponsor_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sponsor_view")
     * @Template
     */
    public function infoAction(Sponsor $entity)
    {
        return [
            'entity' => $entity
        ];
    }
    /**
     * Wird aufgerufen, um im Account einen Abschnitt für die Sponsoren 
     * einzublenden. Die Einbindung erfolgt über die placeholder.yml
     * Die Methode stellt die Sponsoren-Datensätze des aktuellen Accounts
     * im entsprechenden Channel bereit.
     * Die eigentlichen Datensätze werden dann in der Route 
     * dmkclub_sponsor_widget_sponsor_info gerendert.
     * 
     * @Route(
     *      "/widget/sponsor-info/account/{accountId}/channel/{channelId}",
     *      name="dmkclub_sponsor_widget_account_sponsor_info",
     *      requirements={"accountId"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("account", class="OroCRMAccountBundle:Account", options={"id" = "accountId"})
     * @ParamConverter("channel", class="OroCRMChannelBundle:Channel", options={"id" = "channelId"})
     * @AclAncestor("orocrm_sales_b2bcustomer_view")
     * @Template
     */
    public function accountSponsorInfoAction(Account $account, Channel $channel)
    {
        $entities = $this->getDoctrine()
            ->getRepository('DMKClubSponsorBundle:Sponsor')
            ->findBy(['account' => $account, 'dataChannel' => $channel]);

        return ['account' => $account, 'sponsors' => $entities, 'channel' => $channel];
    }

    /**
     * @Route(
     *        "/widget/sponsor-info/{id}/channel/{channelId}",
     *        name="dmkclub_sponsor_widget_sponsor_info",
     *        requirements={"id"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("channel", class="OroCRMChannelBundle:Channel", options={"id" = "channelId"})
     * @AclAncestor("orocrm_magento_customer_view")
     * @Template
     */
    public function sponsorInfoAction(Sponsor $entity, Channel $channel)
    {
        return [
            'sponsor'             => $entity,
            'channel'              => $channel,
//             'leadClassName'        => $this->container->getParameter('orocrm_sales.lead.entity.class'),
//             'opportunityClassName' => $this->container->getParameter('orocrm_sales.opportunity.class'),
        ];
    }
}
