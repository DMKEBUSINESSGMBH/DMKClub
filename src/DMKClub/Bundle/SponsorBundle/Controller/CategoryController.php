<?php

namespace DMKClub\Bundle\SponsorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\SponsorBundle\Entity\Category;
use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\SponsorBundle\Form\Handler\CategoryHandler;
use Symfony\Component\Form\Form;
use Oro\Bundle\FormBundle\Model\UpdateHandler;


/**
 * @Route("/sponsorcategory")
 */
class CategoryController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            CategoryHandler::class,
            'dmkclub.sponsorcategory.form' => Form::class,
            UpdateHandler::class,
        ]);
    }

	/**
	 * @Route("/", name="dmkclub_sponsorcategory_index")
	 * @AclAncestor("dmkclub_sponsorcategory_view")
	 * @Template
	 */
	public function indexAction()
	{
		return [
			'entity_class' => Category::class,
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
        return $this->get(UpdateHandler::class)->update(
            $entity,
            $this->get('dmkclub.sponsorcategory.form'),
            $this->get(TranslatorInterface::class)->trans('dmkclub.controller.sponsorcategory.saved.message'),
            null,
            $this->get(CategoryHandler::class)
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
