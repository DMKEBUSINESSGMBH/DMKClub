<?php
namespace DMKClub\Bundle\MemberBundle\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use DMKClub\Bundle\MemberBundle\Entity\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Oro\Bundle\DashboardBundle\Model\WidgetConfigs;
use Oro\Bundle\ChartBundle\Model\ChartViewBuilder;

class DashboardController extends AbstractController
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            WidgetConfigs::class,
            TranslatorInterface::class,
            ChartViewBuilder::class
        ]);
    }

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
        $translator = $this->get(TranslatorInterface::class);

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

        $widgetAttr = $this->get(WidgetConfigs::class)->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get(ChartViewBuilder::class)
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
        $translator = $this->get(TranslatorInterface::class);

        $chartData = [];
        $data = $this->getMemberRepository()->getMembersGender($this->get(WidgetConfigs::class)
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));
        foreach ($data as $key => $value) {
            $chartData[] = [
                'label' => $translator->trans('dmkclub.member.dashboard.members_gender_chart.' . $key) . ': ' . $value,
                'value' => $value
            ];
        }

        $widgetAttr = $this->get(WidgetConfigs::class)->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get(ChartViewBuilder::class)
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
        $chartData = $this->getMemberRepository()->getNewMembersByYear($this->get(WidgetConfigs::class)
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));

        $widgetAttr = $this->get(WidgetConfigs::class)->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get(ChartViewBuilder::class)
            ->setArrayData($chartData)
            ->setOptions([
                'name' => 'bar_chart',
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
     * @Route("/dmkclub_member_dashboard_members_age_chart/chart/{widget}",
     *      name="dmkclub_member_dashboard_members_age_chart",
     *      requirements={"widget"="[\w-]+"}
     * )
     * @Template("DMKClubMemberBundle:Dashboard:flowChart.html.twig")
     */
    public function memberByAge(Request $request, $widget)
    {
        $chartData = $this->getMemberRepository()->getMemberByAge($this->get(WidgetConfigs::class)
            ->getWidgetOptions($request->query->get('_widgetId', null))
            ->get('memberType'));

        $widgetAttr = $this->get(WidgetConfigs::class)->getWidgetAttributesForTwig($widget);
        $widgetAttr['chartView'] = $this->get(ChartViewBuilder::class)
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
