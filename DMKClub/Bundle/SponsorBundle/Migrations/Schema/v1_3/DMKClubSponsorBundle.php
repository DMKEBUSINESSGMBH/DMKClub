<?php

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;

class DMKClubSponsorBundle implements Migration, ActivityExtensionAwareInterface
{
	/** @var ActivityExtension */
	protected $activityExtension;
	
	/**
	 * {@inheritdoc}
	 */
	public function setActivityExtension(ActivityExtension $activityExtension)
	{
		$this->activityExtension = $activityExtension;
	}
	
	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		self::addActivityAssociations($schema, $this->activityExtension);
	}

    /**
     * Enables activities for sponsor entity
     *
     * @param Schema            $schema
     * @param ActivityExtension $activityExtension
     */
    public static function addActivityAssociations(Schema $schema, ActivityExtension $activityExtension)
    {
        $activityExtension->addActivityAssociation($schema, 'orocrm_call', 'dmkclub_sponsor');
        $activityExtension->addActivityAssociation($schema, 'orocrm_task', 'dmkclub_sponsor');
        $activityExtension->addActivityAssociation($schema, 'oro_calendar_event', 'dmkclub_sponsor');
    }
}
