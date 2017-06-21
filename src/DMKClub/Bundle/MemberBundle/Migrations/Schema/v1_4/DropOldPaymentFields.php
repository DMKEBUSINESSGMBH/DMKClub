<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Doctrine\DBAL\Types\Type;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;

class DropOldPaymentFields implements Migration, OrderedMigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('dmkclub_member_proposal');
        if ($table->hasColumn('payment_interval')) {
            $table->dropColumn('payment_interval');
        }
        if ($table->hasColumn('payment_option')) {
            $table->dropColumn('payment_option');
        }

        $table = $schema->getTable('dmkclub_member');
        if ($table->hasColumn('payment_option')) {
            $table->dropColumn('payment_option');
        }
    }
}
