<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Oro\Bundle\DashboardBundle\Migrations\Data\ORM\AbstractDashboardFixture;

class LoadDashboardData extends AbstractDashboardFixture implements DependentFixtureInterface {
	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function getDependencies() {
		return [
				'Oro\Bundle\DashboardBundle\Migrations\Data\ORM\LoadDashboardData'
		];
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function load(ObjectManager $manager) {
		// to update existing one
		$dashboard = $this->findAdminDashboardModel ( $manager, // pass ObjectManager
			'dmkclub_dashboard' ); // dashboard name
		if (! $dashboard) {
			// create new dashboard
			$dashboard = $this->createAdminDashboardModel ( $manager, // pass ObjectManager
				'dmkclub_dashboard' ); // dashboard name
			$dashboard->setLabel(
					 $this->container->get('translator')->trans('dmkclub.dashboard.title.main')
			);
		}

		if ($dashboard) {
			$dashboard
			// ->addWidget($this->createWidgetModel('opportunities_by_lead_source_chart', [1, 80]))
				->addWidget ( $this->createWidgetModel ( 'members_gender_chart', [1, 20] ))
				->addWidget ( $this->createWidgetModel ( 'members_in_active_chart', [0, 20] ));

			$manager->flush ();
		}
	}
}
