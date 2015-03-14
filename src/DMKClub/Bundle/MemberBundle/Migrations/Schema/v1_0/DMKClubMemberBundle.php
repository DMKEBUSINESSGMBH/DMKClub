<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;

class DMKClubMemberBundle implements Migration
{

	/**
	 * OptionField für Mitgliedsstatus:
	 * - kein Mitglied
	 * - aktives Mitglied
	 * - Ex-Mitglied
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
        $this->createDmkclubMemberTable($schema);

        $this->addDmkclubMemberForeignKeys($schema);
	}

	/**
	 * Create dmkclub_member table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberTable(Schema $schema)
	{
        $table = $schema->createTable('dmkclub_member');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('postal_address', 'integer', ['notnull' => false]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('contact_id', 'integer', ['notnull' => false]);
        $table->addColumn('member_code', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('start_date', 'date', ['notnull' => false]);
        $table->addColumn('end_date', 'date', ['notnull' => false]);
        $table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('created_at', 'datetime', []);
        $table->addColumn('updated_at', 'datetime', []);
        $table->addColumn('is_active', 'boolean', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['contact_id'], 'IDX_6A79FCCDE7A1254A', []);
        $table->addIndex(['postal_address'], 'IDX_6A79FCCD972EFBF7', []);
        $table->addIndex(['user_owner_id'], 'IDX_6A79FCCD9EB185F9', []);
        $table->addIndex(['organization_id'], 'IDX_6A79FCCD32C8A3DE', []);
	}
	
    /**
     * Add dmkclub_member foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubMemberForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_member');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_address'),
            ['postal_address'],
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
