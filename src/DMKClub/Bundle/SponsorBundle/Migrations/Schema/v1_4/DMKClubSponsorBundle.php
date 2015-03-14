<?php

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_4;

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
		$table = $schema->getTable('dmkclub_sponsor');
        $table->addColumn('postal_address_id', 'integer', ['notnull' => false]);
        $table->addIndex(['postal_address_id'], 'IDX_3A9D13D1FD54954B', []);

        $table->addForeignKeyConstraint(
        		$schema->getTable('oro_address'),
        		['postal_address_id'],
        		['id'],
        		['onDelete' => 'SET NULL', 'onUpdate' => null]
        );

	}
}
