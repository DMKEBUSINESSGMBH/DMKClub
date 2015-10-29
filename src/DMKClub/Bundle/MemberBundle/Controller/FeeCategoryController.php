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
use DMKClub\Bundle\MemberBundle\Entity\FeeCategory;


/**
 * @Route("/feecategory")
 */
class FeeCategoryController extends Controller {
	/**
	 * @Route("/", name="dmkclub_feecategory_index")
	 * @AclAncestor("dmkclub_feecategory_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => $this->container->getParameter('dmkclub.feecategory.entity.class')
		];
	}
    /**
     * Create feecategory form
     * @Route("/create", name="dmkclub_feecategory_create")
     * @Template("DMKClubMemberBundle:FeeCategory:update.html.twig")
     * @Acl(
     *      id="dmkclub_feecategory_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubMemberBundle:FeeCategory"
     * )
     */
    public function createAction() {
    	return $this->update(new FeeCategory());
    }
    /**
     * Update feecategory form
     * @Route("/update/{id}", name="dmkclub_feecategory_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_feecategory_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubMemberBundle:FeeCategory"
     * )
     */
    public function updateAction(FeeCategory $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param Category $entity
     *
     * @return array
     */
    protected function update(FeeCategory $entity)
    {
    	return $this->get('oro_form.model.update_handler')->handleUpdate(
    			$entity,
    			$this->get('dmkclub.feecategory.form'),
    			function (FeeCategory $entity) {
    				return array(
    						'route' => 'dmkclub_feecategory_update',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			function (FeeCategory $entity) {
    				return array(
    						'route' => 'dmkclub_feecategory_view',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			$this->get('translator')->trans('dmkclub.controller.feecategory.saved.message'),
    			$this->get('dmkclub.feecategory.form.handler')
    	);
    }
    /**
     * @Route("/view/{id}", name="dmkclub_feecategory_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_feecategory_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubMemberBundle:FeeCategory"
     * )
     * @Template
     */
    public function viewAction(FeeCategory $entity) {
        return ['entity' => $entity];
    }
    /**
     * @Route("/widget/info/{id}", name="dmkclub_feecategory_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_feecategory_view")
     * @Template
     */
    public function infoAction(FeeCategory $entity)
    {
        return [
            'entity' => $entity
        ];
    }

}
