<?php
namespace DMKClub\Bundle\BasicsBundle\DataGrid\Extension\MassAction;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerInterface;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionHandlerArgs;
use Oro\Bundle\EntityConfigBundle\DependencyInjection\Utils\ServiceLink;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionResponse;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberFeeManager;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use DMKClub\Bundle\BasicsBundle\Job\JobExecutor;

class DownloadPdfHandler implements MassActionHandlerInterface {
	const FLUSH_BATCH_SIZE = 100;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var TranslatorInterface
	 */
	protected $translator;

	/** @var \Psr\Log\LoggerInterface */
	private $logger;
	/** @var \DMKClub\Bundle\BasicsBundle\PDF\Manager */
	private $pdfManager;

	protected $router;

	/**
	 * @param EntityManager $entityManager
	 * @param TranslatorInterface $translator
	 * @param ServiceLink $securityFacadeLink
	 */
	public function __construct(
			EntityManager $entityManager,
			TranslatorInterface $translator,
			LoggerInterface $logger,
	        Manager $pdfManager,
			$router
	) {
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->logger = $logger;
		$this->router = $router;
		$this->pdfManager = $pdfManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(MassActionHandlerArgs $args) {
		$data = $args->getData();
		$massAction = $args->getMassAction();
		$options = $massAction->getOptions()->toArray();
 		$query = $args->getResults()->getSource();
// 		$query->getQuery()->getAST()->

		$this->entityManager->beginTransaction();
		try {
			set_time_limit(0);
 			$data = $this->handleExport($options, $data, $query);
			$this->entityManager->commit();
		} catch (\Exception $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		return $this->getResponse($args, $data);
	}

	/**
	 * @param array $options
	 * @param array $data
	 * @param Query $query Die Query des Datagrids
	 * @return int
	 */
	protected function handleExport($options, $data, $query) {
		$isAllSelected = $this->isAllSelected($data);
		$iteration = 0;

		$jobData = [
				'data_identifier' => $options['data_identifier'],
				'entity_name' => $options['entity_name'],
		];

		if (array_key_exists('values', $data) && !empty($data['values'])) {
			$jobData['entity_ids'] = $data['values'];
			$iteration = count(explode(',', $data['values']));
		}
		elseif($isAllSelected) {
			$entityIds = [];
			$result = $query->iterate();
			foreach ($result as $row) {
				$row = reset($row);
				$entityIds[] = $row['id'];
			}
			$jobData['entity_ids'] = implode(',',$entityIds);

//			$this->entityManager->flush();
			$iteration++;
		}
		if(array_key_exists('entity_ids', $jobData)) {

			$ids = explode(',', $jobData['entity_ids']);

			$filename = $this->pdfManager->buildPdfCombined(function($pdfCallBack) use ($ids) {
			    foreach ($ids As $id) {
			        $memberFee = $this->getMemberFeeRepository()->findOneBy(['id' => $id]);
			        $pdfCallBack($memberFee);
			    }
			});
		}

		return ['items'=> $iteration, 'filename'=> $filename];
	}

	/**
	 * @return \DMKClub\Bundle\MemberBundle\Entity\Repository\MemberFeeRepository
	 */
	public function getMemberFeeRepository() {
	    return $this->entityManager->getRepository('DMKClubMemberBundle:MemberFee');
	}

	/**
	 * @param array $data
	 * @return bool
	 */
	protected function isAllSelected($data)
	{
		return array_key_exists('inset', $data) && $data['inset'] === '0';
	}

	/**
	 * @param MassActionHandlerArgs $args
	 * @param int $entitiesCount
	 *
	 * @return MassActionResponse
	 */
	protected function getResponse(MassActionHandlerArgs $args, $data = 0)
	{
	    $entitiesCount = $data['items'];
	    $fileName = $data['filename'];

		$massAction      = $args->getMassAction();
		$responseMessage = 'dmkclub.basics.datagrid.action.success_message';
		$responseMessage = $massAction->getOptions()->offsetGetByPath('[messages][success]', $responseMessage);

		$successful = $entitiesCount > 0;
		$options    = ['count' => $entitiesCount];

		$url = '';
		if($entitiesCount > 0) {
		    $url = $this->router->generate(
		        'oro_importexport_export_download',
		        ['fileName' => basename($fileName)]
	        );
		}
		$options['url'] = $url;


		return new MassActionResponse(
				$successful,
				$this->translator->transChoice(
						$responseMessage,
						$entitiesCount,
						['%count%' => $entitiesCount]
				),
				$options
		);
	}
}
