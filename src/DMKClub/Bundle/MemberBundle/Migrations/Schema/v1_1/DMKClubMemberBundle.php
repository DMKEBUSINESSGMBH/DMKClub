<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;

class DMKClubMemberBundle implements Migration
{

	/**
	 * Add Account and DataChannel
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
		$table = $schema->getTable('dmkclub_member');
		$table->addColumn('account_id', 'integer', ['notnull' => false]);
		$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
		$table->addIndex(['account_id'], 'IDX_6A79FCCD9B6B5FBA', []);
		$table->addIndex(['data_channel_id'], 'IDX_6A79FCCDBDC09B73', []);

		$table->addForeignKeyConstraint(
            $schema->getTable('orocrm_account'),
            ['account_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
		$table->addForeignKeyConstraint(
            $schema->getTable('orocrm_channel'),
            ['data_channel_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
	}


}
