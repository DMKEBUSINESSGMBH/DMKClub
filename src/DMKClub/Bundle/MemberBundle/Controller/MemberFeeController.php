<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use Symfony\Component\HttpFoundation\JsonResponse;
use Oro\Bundle\ImportExportBundle\File\FileSystemOperator;
use FOS\RestBundle\Util\Codes;

/**
 * @Route("/memberfee")
 */
class MemberFeeController extends Controller {
	/**
	 * @Route("/", name="dmkclub_memberfee_index")
	 * @AclAncestor("dmkclub_memberfee_view")
	 * @Template
	 */
	public function indexAction()
	{
	    return [
	        'entity_class' => $this->container->getParameter('dmkclub_member.memberfee.entity.class')
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
		$pdfManager = $this->container->get('dmkclub_basics.pdf.manager');

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

		$response = new JsonResponse($responseData, Codes::HTTP_OK);

		return $response;
	}

	/**
	 * @return FileSystemOperator
	 */
	protected function getFilesystemOperator() {
		return $this->get('oro_importexport.file.file_system_operator');
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
		return $this->get('oro_form.model.update_handler')->handleUpdate(
				$entity,
				$this->get('dmkclub_member.memberfee.form'),
				function (MemberFee $entity) {
					return array(
							'route' => 'dmkclub_memberfee_update',
							'parameters' => array('id' => $entity->getId())
					);
				},
				function (MemberFee $entity) {
					return array(
							'route' => 'dmkclub_memberfee_view',
							'parameters' => array('id' => $entity->getId())
					);
				},
				$this->get('translator')->trans('dmkclub.memberfee.message.saved'),
				$this->get('dmkclub_member.memberfee.form.handler')
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
