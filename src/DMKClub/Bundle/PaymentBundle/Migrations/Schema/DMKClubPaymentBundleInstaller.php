<?php

namespace DMKClub\Bundle\PaymentBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubPaymentBundleInstaller implements Installation
{

	/**
	 * {@inheritdoc}
	 */
	public function getMigrationVersion()
	{
		return 'v1_0';
	}


	/**
	 * {@inheritdoc}
	 */
	public function up(Schema $schema, QueryBag $queries) {
		/** Tables generation **/
		$this->createDmkclubBankaccountTable($schema);
		$this->createDmkclubSepacreditorTable($schema);

		$this->addDmkclubSepacreditorForeignKeys($schema);
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
	/**
	 * Create dmkclub_sepacreditor table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubSepacreditorTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_sepacreditor');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('iban', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('bic', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('creditor_id', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('created', 'datetime', []);
		$table->addColumn('updated', 'datetime', []);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['user_owner_id'], 'IDX_77AFE1949EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_77AFE19432C8A3DE', []);
	}

	/**
	 * Add dmkclub_sepacreditor foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubSepacreditorForeignKeys(Schema $schema) {
		$table = $schema->getTable('dmkclub_sepacreditor');
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

}
