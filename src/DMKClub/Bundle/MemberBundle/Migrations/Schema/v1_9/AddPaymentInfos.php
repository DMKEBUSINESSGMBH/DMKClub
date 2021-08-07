<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_9;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddPaymentInfos implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member_fee');
        if (!$table->hasColumn('remittance_information')) {
            $table->addColumn('remittance_information', 'string', [
                'notnull' => false,
                'length' => 120
            ]);
            $table->addColumn('direct_debit_mandate_id', 'string', [
                'notnull' => false,
                'length' => 50
            ]);
        }
        $table = $schema->getTable('dmkclub_member_billing');
        if (!$table->hasColumn('sign')) {
            $table->addColumn('sign', 'string', [
                'notnull' => false,
                'length' => 50
            ]);
        }
        $table = $schema->getTable('dmkclub_member_proposal_bank');
        if (!$table->hasColumn('direct_debit_mandate_id')) {
            $table->addColumn('direct_debit_mandate_id', 'string', [
                'notnull' => false,
                'length' => 50
            ]);
        }

    }
}
