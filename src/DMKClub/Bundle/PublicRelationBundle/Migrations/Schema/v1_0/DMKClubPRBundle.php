<?php

namespace DMKClub\Bundle\PublicRelationBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;

class DMKClubPRBundle implements Migration
{
	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		$this->createDmkclubPrcategoryTable($schema);
		$this->createDmkclubPrcontactTable($schema);

		$this->addDmkclubPrcategoryForeignKeys($schema);
		$this->addDmkclubPrcontactForeignKeys($schema);
	}

    /**
     * Create dmkclub_prcategory table
     *
     * @param Schema $schema
     */
    protected function createDmkclubPrcategoryTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_prcategory');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_owner_id'], 'IDX_BF540F229EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_BF540F2232C8A3DE', []);
    }

    /**
     * Create dmkclub_prcontact table
     *
     * @param Schema $schema
     */
    protected function createDmkclubPrcontactTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_prcontact');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('category', 'integer', ['notnull' => false]);
        $table->addColumn('account_id', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['category'], 'IDX_32A00C4E64C19C1', []);
        $table->addIndex(['account_id'], 'IDX_32A00C4E9B6B5FBA', []);
        $table->addIndex(['user_owner_id'], 'IDX_32A00C4E9EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_32A00C4E32C8A3DE', []);
    }

    /**
     * Add dmkclub_prcategory foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubPrcategoryForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_prcategory');
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
     * Add dmkclub_prcontact foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubPrcontactForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_prcontact');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_prcategory'),
            ['category'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_account'),
            ['account_id'],
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
}
