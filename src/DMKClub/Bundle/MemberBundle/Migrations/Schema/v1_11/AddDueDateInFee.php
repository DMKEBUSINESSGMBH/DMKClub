<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_11;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddDueDateInFee implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member_fee');
        if (!$table->hasColumn('due_date')) {
            $table->addColumn('due_date', 'date', ['notnull' => false]);
        }
    }
}
