<?php
namespace DMKClub\Bundle\MemberBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

    /**
     *
     * @Route("/dmkclub_member_dashboard_members_in_active_chart/chart/{widget}",
     *      name="dmkclub_member_dashboard_members_in_active_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template("DMKClubMemberBundle:Dashboard:membersActivePassiveChart.html.twig")
     * @param Request $request
     * @param mixed $widget
     * @return array
     */
    public function membersActivePassiveAction(Request $request, $widget)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $data = $this->getMemberRepository()->getMembersActivePassive();

        $chartData = [
            [
                'label' => $translator->trans('dmkclub.member.dashboard.members_in_active_chart.active') . ': ' . $data['active'],
                'value' => $data['active']
            ],
            [
                'label' => $translator->trans('dmkclub.member.dashboard.members_in_active_chart.passive') . ': ' . $data['passive'],
                'value' => $data['passive']
            ]
        ];

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($chartData)
            ->setOptions([
            'name' => 'pie_chart',
            'data_schema' => [
                'label' => [
                    'field_name' => 'label'
                ],
                'value' => [
                    'field_name' => 'value'
                ]
            ]
        ])
            ->getView();

        return $widgetAttr;
    }

    /**
     *
     * @Route("/dmkclub_member_dashboard_members_gender_chart/chart/{widget}",
     *      name="dmkclub_member_dashboard_members_gender_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template("DMKClubMemberBundle:Dashboard:membersGenderChart.html.twig")
     * @param Request $request
     * @param mixed $widget
     * @return array
     */
    public function membersByGenderAction(Request $request, $widget)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');

        $chartData = [];
        $data = $this->getMemberRepository()->getMembersGender($this->get('oro_dashboard.widget_configs')
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));
        foreach ($data as $key => $value) {
            $chartData[] = [
                'label' => $translator->trans('dmkclub.member.dashboard.members_gender_chart.' . $key) . ': ' . $value,
                'value' => $value
            ];
        }

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($chartData)
            ->setOptions([
            'name' => 'pie_chart',
            'data_schema' => [
                'label' => [
                    'field_name' => 'label'
                ],
                'value' => [
                    'field_name' => 'value'
                ]
            ]
        ])
            ->getView();

        return $widgetAttr;
    }

    /**
     *
     * @Route("/dmkclub_member_dashboard_members_new_by_year_chart/chart/{widget}",
     *      name="dmkclub_member_dashboard_members_new_by_year_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template("DMKClubMemberBundle:Dashboard:membersNewByYearChart.html.twig")
     * @param Request $request
     * @param mixed $widget
     * @return array
     */
    public function membersNewByYearAction(Request $request, $widget)
    {
        $chartData = $this->getMemberRepository()->getNewMembersByYear($this->get('oro_dashboard.widget_configs')
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($chartData)
            ->setOptions(array(
            'name' => 'bar_chart',
            'data_schema' => array(
                'label' => array(
                    'field_name' => 'label'
                ),
                'value' => array(
                    'field_name' => 'value'
                )
            )
        ))
            ->getView();

        return $widgetAttr;
    }

    /**
     *
     * @Route("/dmkclub_member_dashboard_members_age_chart/chart/{widget}",
     *      name="dmkclub_member_dashboard_members_age_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template("DMKClubMemberBundle:Dashboard:flowChart.html.twig")
     */
    public function memberByAge(Request $request, $widget)
    {
        $chartData = $this->getMemberRepository()->getMemberByAge($this->get('oro_dashboard.widget_configs')
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));

        $widgetAttr = $this->get('oro_dashboard.widget_configs')->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get('oro_chart.view_builder')
            ->setArrayData($chartData)
            ->setOptions([
            'name' => 'pie_chart',
            'data_schema' => [
                'label' => [
                    'field_name' => 'label'
                ],
                'value' => [
                    'field_name' => 'value'
                ]
            ],
            'settings' => [
                'pie' => [
                    'explode' => 6
                ]
            ]
        ])
            ->getView();

        return $widgetAttr;
    }

    /**
     *
     * @return MemberRepository
     */
    protected function getMemberRepository()
    {
        return $this->getDoctrine()->getRepository('DMKClubMemberBundle:Member');
    }
}
