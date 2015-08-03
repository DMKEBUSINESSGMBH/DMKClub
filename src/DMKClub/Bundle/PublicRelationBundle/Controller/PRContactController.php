<?php

namespace DMKClub\Bundle\PublicRelationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use DMKClub\Bundle\PublicRelationBundle\Entity\PRContact;

/**
 * @Route("/prcontact")
 */
class PRContactController extends Controller {
	/**
	 * @Route("/", name="dmkclub_prcontact_index")
	 * @AclAncestor("dmkclub_prcontact_view")
	 * @Template
	 */
	public function indexAction() {
		return [
				'entity_class' => $this->container->getParameter ( 'dmkclub.prcontact.entity.class' )
		];
	}

	/**
	 * @Route("/view/{id}", name="dmkclub_prcontact_view", requirements={"id"="\d+"}))
	 * @Acl(
	 * id="dmkclub_prcontact_view",
	 * type="entity",
	 * permission="VIEW",
	 * class="DMKClubPublicRelationBundle:PRContact"
	 * )
	 * @Template
	 */
	public function viewAction(PRContact $entity) {
		return [
				'entity' => $entity
		];
	}
	/**
	 * Create form
	 * @Route("/create", name="dmkclub_prcontact_create")
	 * @Template("DMKClubPublicRelationBundle:PRContact:update.html.twig")
	 * @Acl(
	 *   id="dmkclub_prcontact_create",
	 *   type="entity",
	 *   permission="CREATE",
	 *   class="DMKClubPublicRelationBundle:PRContact"
	 * )
	 */
	public function createAction() {
		return $this->update ( new PRContact() );
	}

    /**
     * Update p/r contact form
     * @Route("/update/{id}", name="dmkclub_prcontact_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_prcontact_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubPublicRelationBundle:PRContact"
     * )
     */
    public function updateAction(PRContact $entity)
    {
    	return $this->update($entity);
    }

    /**
     * @param PRContact $entity
     *
     * @return array
     */
    protected function update(PRContact $entity) {
    	return $this->get('oro_form.model.update_handler')->handleUpdate(
    			$entity,
    			$this->get('dmkclub.prcontact.form'),
    			function (PRContact $entity) {
    				return array(
    						'route' => 'dmkclub_prcontact_update',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			function (PRContact $entity) {
    				return array(
    						'route' => 'dmkclub_prcontact_view',
    						'parameters' => array('id' => $entity->getId())
    				);
    			},
    			$this->get('translator')->trans('dmkclub.publicrelation.prcontact.saved.message'),
    			$this->get('dmkclub.prcontact.form.handler')
    	);
    }
}
