<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\SponsorBundle\Entity\Category;


/**
 * @Route("/sponsorcategory")
 */
class CategoryController extends Controller {
	/**
	 * @Route("/", name="dmkclub_sponsorcategory_index")
	 * @AclAncestor("dmkclub_sponsorcategory_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => $this->container->getParameter('dmkclub.sponsorcategory.entity.class')
		];
	}
    /**
     * Create sponsorcategory form
     * @Route("/create", name="dmkclub_sponsorcategory_create")
     * @Template("DMKClubSponsorBundle:Category:update.html.twig")
     * @Acl(
     *      id="dmkclub_sponsorcategory_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubSponsorBundle:Category"
     * )
     */
    public function createAction() {
    	return $this->update(new Category());
    }
    /**
     * Update sponsorcategory form
     * @Route("/update/{id}", name="dmkclub_sponsorcategory_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_sponsorcategory_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubSponsorBundle:Category"
     * )
     */
    public function updateAction(Category $entity)
    {
    	return $this->update($entity);
    }
    /**
     * @param Category $entity
     *
     * @return array
     */
    protected function update(Category $entity)
    {
    	return $this->get('oro_form.model.update_handler')->handleUpdate(
    			$entity,
    			$this->get('dmkclub.sponsorcategory.form'),
    			function (Category $entity) {
    				return array(
    						'route' => 'dmkclub_sponsorcategory_update',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			function (Category $entity) {
    				return array(
    						'route' => 'dmkclub_sponsorcategory_view',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			$this->get('translator')->trans('dmkclub.controller.sponsorcategory.saved.message'),
    			$this->get('dmkclub.sponsorcategory.form.handler')
    	);
    }
    /**
     * @Route("/view/{id}", name="dmkclub_sponsorcategory_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_sponsorcategory_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubSponsorBundle:Category"
     * )
     * @Template
     */
    public function viewAction(Category $entity) {
        return ['entity' => $entity];
    }
    /**
     * @Route("/widget/info/{id}", name="dmkclub_sponsorcategory_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sponsorcategory_view")
     * @Template
     */
    public function infoAction(Category $entity)
    {
        return [
            'entity' => $entity
        ];
    }

}
