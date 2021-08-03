<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_10;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Entity\MemberProposal;

class OptimizeDataAudit implements Migration
{

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addQuery(new UpdateEntityConfigFieldValueQuery(
            MemberFee::class, 'member', 'dataaudit', 'auditable', false));
        $queries->addQuery(new UpdateEntityConfigFieldValueQuery(
            MemberFee::class, 'billing', 'dataaudit', 'auditable', false));
        $queries->addQuery(new UpdateEntityConfigFieldValueQuery(
            MemberProposal::class, 'member', 'dataaudit', 'auditable', false));

    }
}
