<?php
namespace DMKClub\Bundle\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\PaymentBundle\Entity\SepaCreditor;

/**
 * @Route("/sepacreditor")
 */
class SepaCreditorController extends AbstractController
{

    /**
     * @Route("/", name="dmkclub_sepacreditor_index")
     * @AclAncestor("dmkclub_sepacreditor_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('dmkclub.payment.sepacreditor.entity.class')
        ];
    }

    /**
     * Create sepacreditor form
     * @Route("/create", name="dmkclub_sepacreditor_create")
     * @Template("DMKClubPaymentBundle:SepaCreditor:update.html.twig")
     * @Acl(
     *   id="dmkclub_sepacreditor_create",
     *   type="entity",
     *   permission="CREATE",
     *   class="DMKClubPaymentBundle:SepaCreditor"
     * )
     */
    public function createAction()
    {
        return $this->update(new SepaCreditor());
    }

    /**
     * Update sepacreditor form
     * @Route("/update/{id}", name="dmkclub_sepacreditor_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     * id="dmkclub_sepacreditor_update",
     * type="entity",
     * permission="EDIT",
     * class="DMKClubPaymentBundle:SepaCreditor"
     * )
     */
    public function updateAction(SepaCreditor $entity)
    {
        return $this->update($entity);
    }

    /**
     *
     * @param SepaCreditor $entity
     *
     * @return array
     */
    protected function update(SepaCreditor $entity)
    {
        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('dmkclub_payment.sepacreditor.form'),
            function (SepaCreditor $entity) {
                return [
                    'route' => 'dmkclub_sepacreditor_update',
                    'parameters' => [
                        'id' => $entity->getId()
                    ]
                ];
            }, function (SepaCreditor $entity) {
                return [
                    'route' => 'dmkclub_sepacreditor_view',
                    'parameters' => [
                        'id' => $entity->getId()
                    ]
                ];
            },
            $this->get('translator')->trans('dmkclub.payment.sepacreditor.saved.message'),
            $this->get('dmkclub_payment.sepacreditor.form.handler')
        );
    }

    /**
     * @Route("/view/{id}", name="dmkclub_sepacreditor_view", requirements={"id"="\d+"}))
     * @Acl(
     * id="dmkclub_sepacreditor_view",
     * type="entity",
     * permission="VIEW",
     * class="DMKClubPaymentBundle:SepaCreditor"
     * )
     * @Template
     */
    public function viewAction(SepaCreditor $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    /**
     * @Route("/widget/info/{id}", name="dmkclub_sepacreditor_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_sepacreditor_view")
     * @Template
     */
    public function infoAction(SepaCreditor $entity)
    {
        return [
            'entity' => $entity
        ];
    }
}
