<?php
namespace DMKClub\Bundle\MemberBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberBillingManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DMKClub\Bundle\MemberBundle\Form\Handler\CreateBillsHandler;
use Symfony\Component\Form\Form;
use Oro\Bundle\FormBundle\Model\UpdateHandler;
use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberBillingHandler;

/**
 * @Route("/memberbilling")
 */
class MemberBillingController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            MemberBillingManager::class,
            MemberBillingHandler::class,
            CreateBillsHandler::class,
            'dmkclub_member.createbills.form' => Form::class,
            'dmkclub_member.memberbilling.form' => Form::class,
            UpdateHandler::class,
        ]);
    }


    /**
     * @Route("/", name="dmkclub_memberbilling_index")
     * @AclAncestor("dmkclub_memberbilling_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => MemberBilling::class,
        ];
    }

    /**
     * Create member form
     * @Route("/create", name="dmkclub_memberbilling_create")
     * @Template("DMKClubMemberBundle:MemberBilling:update.html.twig")
     * @Acl(
     *   id="dmkclub_memberbilling_create",
     *   type="entity",
     *   permission="CREATE",
     *   class="DMKClubMemberBundle:MemberBilling"
     * )
     */
    public function createAction()
    {
        $billing = new MemberBilling();
        $billing->setPositionLabels($this->get(TranslatorInterface::class)->trans('dmkclub.member.memberbilling.position_labels.default'));
        return $this->update($billing);
    }

    /**
     * Update memberbilling form
     * @Route("/update/{id}", name="dmkclub_memberbilling_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     * id="dmkclub_memberbilling_update",
     * type="entity",
     * permission="EDIT",
     * class="DMKClubMemberBundle:MemberBilling"
     * )
     */
    public function updateAction(MemberBilling $entity)
    {
        return $this->update($entity);
    }

    /**
     *
     * @param MemberBilling $entity
     *
     * @return array
     */
    protected function update(MemberBilling $entity)
    {
        /* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandler */
        $handler = $this->get(UpdateHandler::class);
        return $handler->handleUpdate(
            $entity,
            $this->get('dmkclub_member.memberbilling.form'),
            // SaveAndStayRoute
            function (MemberBilling $entity) {
                return [
                    'route' => 'dmkclub_memberbilling_update',
                    'parameters' => [
                        'id' => $entity->getId()
                    ]
                ];
            },
            // SaveAndCloseRoute
            function (MemberBilling $entity) {
                return [
                    'route' => 'dmkclub_memberbilling_view',
                    'parameters' => [
                        'id' => $entity->getId()
                    ]
                ];
            },
            $this->get(TranslatorInterface::class)->trans('dmkclub.member.memberbilling.message.saved'),
            $this->get(MemberBillingHandler::class)
        );
    }

    /**
     * @Route("/view/{id}", name="dmkclub_memberbilling_view", requirements={"id"="\d+"}))
     * @Acl(
     * id="dmkclub_memberbilling_view",
     * type="entity",
     * permission="VIEW",
     * class="DMKClubMemberBundle:MemberBilling"
     * )
     * @Template
     */
    public function viewAction(MemberBilling $entity)
    {
        $options = $this->getBillingManager()->getProcessorSettings($entity);
        $options = $this->getBillingManager()
            ->getProcessor($entity)
            ->formatSettings($options);
        $entity->setPayedTotal($this->getBillingManager()
            ->getPaidTotal($entity));

        return [
            'entity' => $entity,
            'options' => $options
        ];
    }

    /**
     *
     * @return MemberBillingManager
     */
    protected function getBillingManager()
    {
        return $this->get(MemberBillingManager::class);
    }

    /**
     * Wird zur Anzeige des Info-Widgets in der Detailansicht verwendet
     * @Route("/widget/info/{id}", name="dmkclub_memberbilling_widget_info", requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_memberbilling_view")
     * @Template
     */
    public function infoAction(MemberBilling $entity)
    {
        return [
            'entity' => $entity
        ];
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function getCreateBillsForm()
    {
        return $this->get('dmkclub_member.createbills.form');
    }
    /**
     * @Route("/{id}/createbills", name="dmkclub_memberbilling_createbills", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_memberbilling_create")
     * @Template
     */
    public function createBillsAction(MemberBilling $entity)
    {
        $form = $this->getCreateBillsForm();
        $response = [
            'entity' => $entity,
            'saved' => false,
            'form' => $form->createView(),
        ];

        // Form auswerten
        if ($ret = $this->get(CreateBillsHandler::class)->process($entity)) {
            $response['message'] = $this->buildMessage('dmkclub.member.memberbilling.message.accounting' . ($ret['async'] ? '.async' : '') . '.started', $ret);
            $response['saved'] = true;
        }

        return $response;
    }

    /**
     * @Route("/{id}/refreshsummary", name="dmkclub_memberbilling_refreshsummary", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_memberbilling_create")
     * @Template
     */
    public function refreshSummaryAction(MemberBilling $entity)
    {
        // Info an den Manager übergeben
        $this->getBillingManager()->updateSummary($entity);

        return new RedirectResponse($this->generateUrl('dmkclub_memberbilling_view', [
            'id' => $entity->getId()
        ]));
    }

    /**
     * @Route("/recreatecorrections/{id}", name="dmkclub_memberbilling_createcorrections", requirements={"id"="\d+"})
     * @AclAncestor("dmkclub_memberbilling_create")
     * @Template
     */
    public function createCorrectionsAction(MemberBilling $entity)
    {
        // Info an den Manager übergeben
        $ret = $this->get(MemberBillingManager::class)->startCorrections($entity);

        $marked = ((int) $ret['success'] + (int) $ret['skipped']) > 0;
        $msgType = $marked ? 'success' : 'warning';
        $msg = 'dmkclub.member.memberbilling.message.correction.' . ($marked ? 'started' : 'nothingfound');
        $this->get('session')
            ->getFlashBag()
            ->add($msgType, $this->buildMessage($msg, $ret));
        return new RedirectResponse($this->generateUrl('dmkclub_memberbilling_view', [
            'id' => $entity->getId()
        ]));
    }

    private function buildMessage($msg, $info)
    {
        $msg = $this->get(TranslatorInterface::class)->trans($msg);
        return sprintf($msg, $info['success'], $info['skipped'], $info['errors']);
    }
}
