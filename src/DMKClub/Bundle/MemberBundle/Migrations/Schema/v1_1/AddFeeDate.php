<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddFeeDate implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member_fee');
        if ($table->hasColumn('bill_date')) {
            return;
        }
        $table->addColumn('bill_date', 'date', ['notnull' => false]);

        $queries->addPostQuery(new UpdateBillDateQuery());
    }
}
