<?php

namespace DMKClub\Bundle\PaymentBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;

class DMKClubPaymentBundle implements Migration
{

	/**
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
        $this->createDmkclubBankaccountTable($schema);

	}

	/**
	 * Create dmkclub_bankaccount table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubBankaccountTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_bankaccount');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('account_owner', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('iban', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('bic', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('bank_name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('created', 'datetime', []);
		$table->addColumn('updated', 'datetime', []);
		$table->setPrimaryKey(['id']);
	}

}
