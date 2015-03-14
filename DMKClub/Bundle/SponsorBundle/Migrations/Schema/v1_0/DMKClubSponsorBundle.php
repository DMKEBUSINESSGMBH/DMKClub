<?php

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;

class DMKClubSponsorBundle implements Migration
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries) {
        $this->createDmkclubSponsorTable($schema);
        $this->createDmkclubSponsorcategoryTable($schema);

        $this->addDmkclubSponsorForeignKeys($schema);
    }
    /**
     * Create dmkclub_sponsor table
     *
     * @param Schema $schema
     */
    protected function createDmkclubSponsorTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_sponsor');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('category', 'integer', ['notnull' => false]);
        $table->addColumn('account_id', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('contact_id', 'integer', ['notnull' => false]);
        $table->addColumn('start_date', 'date', ['notnull' => false]);
        $table->addColumn('end_date', 'date', ['notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->addColumn('is_active', 'boolean', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['contact_id'], 'IDX_3A9D13D1E7A1254A', []);
        $table->addIndex(['account_id'], 'IDX_3A9D13D19B6B5FBA', []);
        $table->addIndex(['user_owner_id'], 'IDX_3A9D13D19EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_3A9D13D132C8A3DE', []);
        $table->addIndex(['category'], 'IDX_3A9D13D164C19C1', []);
        $table->addIndex(['data_channel_id'], 'IDX_3A9D13D1BDC09B73', []);
    }

    /**
     * Create dmkclub_sponsor_to_category table
     *
     * @param Schema $schema
     */
    protected function createDmkclubSponsorToCategoryTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_sponsor_to_category');
        $table->addColumn('sponsor_id', 'integer', []);
        $table->addColumn('sponsor_category_id', 'integer', []);
        $table->setPrimaryKey(['sponsor_id', 'sponsor_category_id']);
        $table->addIndex(['sponsor_id'], 'IDX_EAEFA7CD12F7FB51', []);
        $table->addIndex(['sponsor_category_id'], 'IDX_EAEFA7CD44B2CEF9', []);
    }

    /**
     * Create dmkclub_sponsorcategory table
     *
     * @param Schema $schema
     */
    protected function createDmkclubSponsorcategoryTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_sponsorcategory');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->setPrimaryKey(['id']);
    }



    /**
     * Add dmkclub_sponsor foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubSponsorForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_sponsor');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_channel'),
            ['data_channel_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_sponsorcategory'),
            ['category'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
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
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_contact'),
            ['contact_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

}
