<?php

namespace DMKClub\Bundle\MemberBundle\Controller\Dashboard;

use Oro\Bundle\EntityExtendBundle\Twig\EnumExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use OroCRM\Bundle\SalesBundle\Entity\Repository\SalesFunnelRepository;

class DashboardController extends Controller {
	/**
	* @Route("/dmkclub_member_dashboard_members_in_active_chart/chart/{widget}",
	*      name="dmkclub_member_dashboard_members_in_active_chart",
	*      requirements={"widget"="[\w-]+"}
	* )
	* @Template("DMKClubMemberBundle:Dashboard:membersInActiveChart.html.twig")
	*/
	public function membersInActiveAction($widget) {
		/** @var TranslatorInterface $translator */
		$translator = $this->get('translator');

		/** @var EnumExtension $enumValueTranslator */
		$enumValueTranslator = $this->get('oro_entity_extend.twig.extension.enum');

		$data = $this->getDoctrine()->getRepository('DMKClubMemberBundle:Member')->getMembersInActive();

		$chartData = [
				[
					'label' => 'active',
					'value' => $data['active'],
				],
				[
						'label' => 'passive',
						'value' => $data['passive'],
				]
		];

		$widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
		$widgetAttr['chartView'] = $this->get('oro_chart.view_builder')->setArrayData($chartData)
			->setOptions(array(
				'name' => 'pie_chart',
				'data_schema' => array(
						'label' => array(
								'field_name' => 'label'
						),
						'value' => array(
								'field_name' => 'value'
						)
				)
		))->getView();

		return $widgetAttr;
	}


	/**
	* @Route("/dmkclub_member_dashboard_members_gender_chart/chart/{widget}",
	*      name="dmkclub_member_dashboard_members_gender_chart",
	*      requirements={"widget"="[\w-]+"}
	* )
	* @Template("DMKClubMemberBundle:Dashboard:membersGenderChart.html.twig")
	*/
	public function membersByGenderAction($widget) {
		/** @var TranslatorInterface $translator */
		$translator = $this->get('translator');

		/** @var EnumExtension $enumValueTranslator */
		$enumValueTranslator = $this->get('oro_entity_extend.twig.extension.enum');

		$chartData = [];
		$data = $this->getDoctrine()->getRepository('DMKClubMemberBundle:Member')->getMembersGender();
		foreach ($data As $key => $value) {
			$chartData[] = [
					'label' => $translator->trans('dmkclub.member.dashboard.members_gender_chart.'.$key),
					'value' => $value,
			];
		}

		$widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
		$widgetAttr['chartView'] = $this->get('oro_chart.view_builder')->setArrayData($chartData)
			->setOptions(array(
				'name' => 'pie_chart',
				'data_schema' => array(
						'label' => array(
								'field_name' => 'label'
						),
						'value' => array(
								'field_name' => 'value'
						)
				)
		))->getView();

		return $widgetAttr;
	}
}
