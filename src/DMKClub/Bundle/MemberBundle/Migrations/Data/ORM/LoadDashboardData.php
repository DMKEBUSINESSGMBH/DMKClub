<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Oro\Bundle\DashboardBundle\Migrations\Data\ORM\AbstractDashboardFixture;

class LoadDashboardData extends AbstractDashboardFixture implements DependentFixtureInterface
{
	/**
	 * @ERROR!!!
	 */
	public function getDependencies()
	{
		return ['Oro\Bundle\DashboardBundle\Migrations\Data\ORM\LoadDashboardData'];
	}

	/**
	 * @ERROR!!!
	 */
	public function load(ObjectManager $manager) {
		// to update existing one
		$dashboard = $this->findAdminDashboardModel($manager, 'dmkclub_dashboard' );
		if (! $dashboard) {
			// create new dashboard
			$dashboard = $this->createAdminDashboardModel ( $manager,'dmkclub_dashboard' );
			$dashboard->setLabel(
					 $this->container->get('translator')->trans('dmkclub.dashboard.title.main')
			);
		}

		if ($dashboard) {
			$dashboard
				->addWidget ( $this->createWidgetModel ( 'members_age_chart', [1, 30] ))
				->addWidget ( $this->createWidgetModel ( 'members_gender_chart', [1, 20] ))
				->addWidget ( $this->createWidgetModel ( 'members_new_by_year_chart', [0, 30] ))
				->addWidget ( $this->createWidgetModel ( 'members_in_active_chart', [0, 20] ));

			$manager->flush ();
		}
	}
}
