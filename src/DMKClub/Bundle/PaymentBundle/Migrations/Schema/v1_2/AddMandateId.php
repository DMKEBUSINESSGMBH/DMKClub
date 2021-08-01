<?php

namespace DMKClub\Bundle\PaymentBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddMandateId implements Migration
{

	/**
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
	    $table = $schema->getTable('dmkclub_bankaccount');
	    $table->addColumn('direct_debit_mandate_id', 'string', [
	        'notnull' => false,
	        'length' => 50
	    ]);
	}
}
