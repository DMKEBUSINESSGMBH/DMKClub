<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddDiscountToProposal implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member_proposal');
        if ($table->hasColumn('discount_start_date')) {
            return;
        }
        $table->addColumn('discount_start_date', 'date', ['notnull' => false]);
        $table->addColumn('discount_end_date', 'date', ['notnull' => false]);
        $table->addColumn('discount_reason', 'string', ['notnull' => false, 'length' => 255]);
    }
}
