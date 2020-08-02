<?php

namespace DMKClub\Bundle\SponsorBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubSponsorBundle implements Migration
{
	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		$table = $schema->getTable('dmkclub_sponsor');
		$table->addColumn('billing_address_id', 'integer', ['notnull' => false]);
		$table->addIndex(['billing_address_id'], 'IDX_3A9D13D179D0C0E4', []);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_address'),
				['billing_address_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}
}
