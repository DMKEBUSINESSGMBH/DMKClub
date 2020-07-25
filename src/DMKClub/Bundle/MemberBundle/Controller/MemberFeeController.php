<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Form;
use Oro\Bundle\FormBundle\Model\UpdateHandler;
use DMKClub\Bundle\MemberBundle\Form\EntityField\Handler\MemberFeeHandler;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;

/**
 * @Route("/memberfee")
 */
class MemberFeeController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            MemberFeeHandler::class,
            Manager::class,
//            'dmkclub_member.memberfee.form' => Form::class,
            UpdateHandler::class,
            RouterInterface::class,
        ]);
    }

	/**
	 * @Route("/", name="dmkclub_memberfee_index")
	 * @AclAncestor("dmkclub_memberfee_view")
	 * @Template
	 */
	public function indexAction()
	{
	    return [
	        'entity_class' => MemberFee::class,
	    ];
	}
	/**
	 * Create member form
	 * @Route("/create", name="dmkclub_memberfee_create")
	 * @Template("DMKClubMemberBundle:MemberFee:update.html.twig")
	 * @Acl(
	 *      id="dmkclub_memberfee_create",
	 *      type="entity",
	 *      permission="CREATE",
	 *      class="DMKClubMemberBundle:MemberFee"
	 * )
	 */
	public function createAction()
	{
		return $this->update(new MemberFee());
	}
	/**
	 * Method is called from "dmkgetpdf" datagrid-action.
	 * Is creates a PDF-File for a single MemberFee and returns a download link.
	 *
	 * @Route("/pdf/{id}", name="dmkclub_memberfee_createpdf", requirements={"id"="\d+"}, defaults={"id"=0})
	 * @AclAncestor("dmkclub_memberfee_view")
	 * -> nicht notwendig! Template("DMKClubMemberBundle:MemberFee:pdf.html.twig")
	 */
	public function createPDFAction(MemberFee $entity) {
		$responseData = [
				'url'  => false
		];

		/* @var $pdfManager \DMKClub\Bundle\BasicsBundle\PDF\Manager */
		$pdfManager = $this->container->get(Manager::class);

		try {
			$file = $pdfManager->buildPdf($entity);
			$url = $this->get('router')->generate(
					'oro_importexport_export_download',
					['fileName' => $file->getKey()]
			);
			$responseData['url'] = $url;
		}
		catch(\Exception $e) {
			$responseData['message'] = $e->getMessage();
		}

		$response = new JsonResponse($responseData, Response::HTTP_OK);

		return $response;
	}

	/**
	 * Update memberfee form
	 * @Route("/update/{id}", name="dmkclub_memberfee_update", requirements={"id"="\d+"}, defaults={"id"=0})
	 *
	 * @Template
	 * @Acl(
	 *      id="dmkclub_memberfee_update",
	 *      type="entity",
	 *      permission="EDIT",
	 *      class="DMKClubMemberBundle:MemberFee"
	 * )
	 */
	public function updateAction(MemberFee $entity)
	{
		return $this->update($entity);
	}
	/**
	 * @param MemberFee $entity
	 *
	 * @return array
	 */
	protected function update(MemberFee $entity)
	{
		return $this->get(UpdateHandler::class)->handleUpdate(
				$entity,
				$this->get('dmkclub_member.memberfee.form'),
				function (MemberFee $entity) {
					return [
						'route' => 'dmkclub_memberfee_update',
						'parameters' => ['id' => $entity->getId()]
					];
				},
				function (MemberFee $entity) {
					return [
						'route' => 'dmkclub_memberfee_view',
						'parameters' => ['id' => $entity->getId()]
					];
				},
				$this->get(TranslatorInterface::class)->trans('dmkclub.memberfee.message.saved'),
				$this->get(MemberFeeHandler::class)
		);
	}

	/**
	 * @Route("/view/{id}", name="dmkclub_memberfee_view", requirements={"id"="\d+"}))
	 * @Acl(
	 *      id="dmkclub_memberfee_view",
	 *      type="entity",
	 *      permission="VIEW",
	 *      class="DMKClubMemberBundle:MemberFee"
	 * )
	 * @Template
	 */
	public function viewAction(MemberFee $entity) {
	    return ['entity' => $entity];
	}

}
