<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;

class AddActivities implements Migration, ActivityExtensionAwareInterface, CommentExtensionAwareInterface {
	/**
	 * @var ActivityExtension
	 */
	protected $activityExtension;
	/** @var CommentExtension */
	protected $comment;

	/**
	 * @param CommentExtension $commentExtension
	 */
	public function setCommentExtension(CommentExtension $commentExtension)
	{
		$this->comment = $commentExtension;
	}

	/**
	 * @inheritdoc
	 */
	public function setActivityExtension(ActivityExtension $activityExtension) {
		$this->activityExtension = $activityExtension;
	}

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		self::addActivityAssociations ( $schema, $this->activityExtension );
		$this->comment->addCommentAssociation($schema, 'dmkclub_member');
	}

	/**
	 * Enables activities for member entity
	 *
	 * @param Schema $schema
	 * @param ActivityExtension $activityExtension
	 */
	public static function addActivityAssociations(Schema $schema, ActivityExtension $activityExtension) {
		$activityExtension->addActivityAssociation ( $schema, 'orocrm_call', 'dmkclub_member' );
		$activityExtension->addActivityAssociation ( $schema, 'orocrm_task', 'dmkclub_member' );
		$activityExtension->addActivityAssociation ( $schema, 'oro_calendar_event', 'dmkclub_member' );
	}
}
