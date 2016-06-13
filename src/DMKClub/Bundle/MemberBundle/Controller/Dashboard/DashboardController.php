<?php

namespace DMKClub\Bundle\MemberBundle\Controller\Dashboard;

use Oro\Bundle\EntityExtendBundle\Twig\EnumExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use OroCRM\Bundle\SalesBundle\Entity\Repository\SalesFunnelRepository;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;

class DashboardController extends Controller {
	/**
	* @Route("/dmkclub_member_dashboard_members_in_active_chart/chart/{widget}",
	*      name="dmkclub_member_dashboard_members_in_active_chart",
	*      requirements={"widget"="[\w-]+"}
	* )
	* @Template("DMKClubMemberBundle:Dashboard:membersActivePassiveChart.html.twig")
	*/
	public function membersActivePassiveAction($widget) {
		/** @var TranslatorInterface $translator */
		$translator = $this->get('translator');

		$data = $this->getMemberRepository()->getMembersActivePassive();

		$chartData = [
				[
					'label' => $translator->trans('dmkclub.member.dashboard.members_in_active_chart.active') . ': '.$data['active'],
					'value' => $data['active'],
				],
				[
						'label' => $translator->trans('dmkclub.member.dashboard.members_in_active_chart.passive') .': '.$data['passive'],
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

		$chartData = [];
		$data = $this->getMemberRepository()->getMembersGender($this->get('oro_dashboard.widget_configs')
                    ->getWidgetOptions($this->getRequest()->query->get('_widgetId', null))
                    ->get('memberType'));
		foreach ($data As $key => $value) {
			$chartData[] = [
					'label' => $translator->trans('dmkclub.member.dashboard.members_gender_chart.'.$key) .': '.$value,
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

	/**
	 * @Route("/dmkclub_member_dashboard_members_new_by_year_chart/chart/{widget}",
	 *      name="dmkclub_member_dashboard_members_new_by_year_chart",
	 *      requirements={"widget"="[\w-]+"}
	 * )
	 * @Template("DMKClubMemberBundle:Dashboard:membersNewByYearChart.html.twig")
	 */
	public function membersNewByYearAction($widget) {

		$chartData = $this->getMemberRepository()->getNewMembersByYear($this->get('oro_dashboard.widget_configs')
                    ->getWidgetOptions($this->getRequest()->query->get('_widgetId', null))
                    ->get('memberType'));

		$widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
		$widgetAttr['chartView'] = $this->get('oro_chart.view_builder')->setArrayData($chartData)
		->setOptions(array(
				'name' => 'bar_chart',
				'data_schema' => array(
						'label' => array(
								'field_name' => 'label'
						),
						'value' => array(
								'field_name' => 'value'
						)
				),
		))->getView();

		return $widgetAttr;
	}

	/**
	 * @Route("/dmkclub_member_dashboard_members_age_chart/chart/{widget}",
	 *      name="dmkclub_member_dashboard_members_age_chart",
	 *      requirements={"widget"="[\w-]+"}
	 * )
	 * @Template("DMKClubMemberBundle:Dashboard:flowChart.html.twig")
	 */
	public function memberByAge($widget) {
		$chartData = $this->getMemberRepository()->getMemberByAge($this->get('oro_dashboard.widget_configs')
                    ->getWidgetOptions($this->getRequest()->query->get('_widgetId', null))
                    ->get('memberType'));

		$widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
		$widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
			->setArrayData($chartData)
			->setOptions(array(
				'name' => 'pie_chart',
				'data_schema' => array(
					'label' => array('field_name' => 'label'),
					'value' => array('field_name' => 'value'),
				),
				'settings' => array(
						'pie' => [
							'explode' => 6,
						],
				),
		))->getView();

		return $widgetAttr;
	}
	/**
	 * @return MemberRepository
	 */
	protected function getMemberRepository() {
	 return $this->getDoctrine()->getRepository('DMKClubMemberBundle:Member');
	}
}
