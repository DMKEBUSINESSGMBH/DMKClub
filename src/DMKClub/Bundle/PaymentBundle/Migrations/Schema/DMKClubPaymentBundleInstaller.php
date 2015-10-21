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
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
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
