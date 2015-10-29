<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;

class AddFeeCategory implements Migration {

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		$this->createDmkclubFeecategoryTable($schema);

		$table = $schema->getTable('dmkclub_member');

    $table->addColumn('fee_category', 'integer', ['notnull' => false]);

    $this->addDmkclubFeecategoryForeignKeys($schema);
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_feecategory'),
				['fee_category'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}

	/**
	 * Add dmkclub_feecategory foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubFeecategoryForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_feecategory');
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_organization'),
				['organization_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_user'),
				['user_owner_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}

	/**
	 * Create dmkclub_feecategory table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubFeecategoryTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_feecategory');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('name', 'string', ['length' => 255]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['user_owner_id'], 'IDX_291829209EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_2918292032C8A3DE', []);
	}

}
