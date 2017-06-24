<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddMemberCodeInt implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member');
        if ($table->hasColumn('member_code_int')) {
            return;
        }
        $table->addColumn('member_code_int', 'integer', ['notnull' => false, 'default' => 0]);

        $queries->addPostQuery(new UpdateMemberCodeQuery());
    }
}
