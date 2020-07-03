<?php
namespace DMKClub\Bundle\PublicRelationBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class AddChannelField implements Migration
{

    /**
     *
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_prcontact');

        $table->addColumn('data_channel_id', 'integer', [
            'notnull' => false
        ]);
        $table->addIndex([
            'data_channel_id'
        ], 'IDX_DMKCLB_PRC_DC', []);

        $table->addForeignKeyConstraint($schema->getTable('orocrm_channel'), [
            'data_channel_id'
        ], [
            'id'
        ], [
            'onDelete' => 'SET NULL',
            'onUpdate' => null
        ]);
    }
}
