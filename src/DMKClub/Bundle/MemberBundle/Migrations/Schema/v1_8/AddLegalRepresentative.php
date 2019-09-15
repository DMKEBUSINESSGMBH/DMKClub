<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_8;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddLegalRepresentative implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member');
        if ($table->hasColumn('legal_contact_id')) {
            return;
        }

        $table->addColumn('legal_contact_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint($schema->getTable('orocrm_contact'), [
            'legal_contact_id'
        ], [
            'id'
        ], [
            'onDelete' => 'SET NULL',
            'onUpdate' => null
        ]);

    }
}
