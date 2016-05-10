<?php

namespace DMKClub\Bundle\BasicsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroCRM\Bundle\AccountBundle\Entity\Account;
use OroCRM\Bundle\ChannelBundle\Entity\Channel;
use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/twigtemplate")
 */
class TwigTemplateController extends Controller
{
	/**
	 * @Route("/", name="dmkclub_basics_twigtemplate_index")
	 * @AclAncestor("dmkclub_basics_twigtemplate_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
		    'entity_class' => $this->container->getParameter('dmkclub_basics.twigtemplate.entity.class')
		];
	}
	/**
	 * Create template form
	 * @Route("/create", name="dmkclub_basics_twigtemplate_create")
	 * @Template("DMKClubBasicsBundle:TwigTemplate:update.html.twig")
	 * @Acl(
	 *      id="dmkclub_basics_twigtemplate_create",
	 *      type="entity",
	 *      permission="CREATE",
	 *      class="DMKClubBasicsBundle:TwigTemplate"
	 * )
	 */
	public function createAction()
	{
		return $this->update(new TwigTemplate());
	}
	/**
	 * Update twigtemplate form
	 * @Route("/update/{id}", name="dmkclub_basics_twigtemplate_update", requirements={"id"="\d+"}, defaults={"id"=0})
	 *
	 * @Template
	 * @Acl(
	 *      id="dmkclub_basics_twigtemplate_update",
	 *      type="entity",
	 *      permission="EDIT",
	 *      class="DMKClubBasicsBundle:TwigTemplate"
	 * )
	 */
	public function updateAction(TwigTemplate $entity)
	{
		return $this->update($entity);
	}
	/**
	 * @param TwigTemplate $entity
	 *
	 * @return array
	 */
	protected function update(TwigTemplate $entity)
	{
		return $this->get('oro_form.model.update_handler')->handleUpdate(
				$entity,
				$this->get('dmkclub_basics.twigtemplate.form'),
				function (TwigTemplate $entity) {
					return array(
							'route' => 'dmkclub_basics_twigtemplate_update',
							'parameters' => array('id' => $entity->getId())
					);
				},
				function (TwigTemplate $entity) {
					return array(
							'route' => 'dmkclub_basics_twigtemplate_view',
							'parameters' => array('id' => $entity->getId())
					);
				},
				$this->get('translator')->trans('dmkclub.basics.twigtemplate.message.saved'),
				$this->get('dmkclub_basics.twigtemplate.form.handler')
		);
	}

	/**
	 * @Route("/view/{id}", name="dmkclub_basics_twigtemplate_view", requirements={"id"="\d+"}))
	 * @Acl(
	 *      id="dmkclub_basics_twigtemplate_view",
	 *      type="entity",
	 *      permission="VIEW",
	 *      class="DMKClubBasicsBundle:TwigTemplate"
	 * )
	 * @Template
	 */
	public function viewAction(TwigTemplate $entity)
	{
		return ['entity' => $entity, ];
	}

	/**
	 * Wird zur Anzeige des Info-Widgets in der Detailansicht verwendet
	 * @Route("/widget/info/{id}", name="dmkclub_basics_twigtemplate_widget_info", requirements={"id"="\d+"}))
	 * @AclAncestor("dmkclub_basics_twigtemplate_view")
	 * @Template
	 */
	public function infoAction(TwigTemplate $entity)
	{
		return ['entity' => $entity];
	}

}
