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

class UpdatePaymentFields implements Migration, OrderedMigrationInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;
    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        // PaymentOption bei Member und Proposal umstellen
        // PaymentInterval bei Proposal umstellen
        $codes = [
            PaymentInterval::YEARLY,
            PaymentInterval::HALF_YEARLY,
            PaymentInterval::QUARTERLY,
            PaymentInterval::MONTHLY,
        ];

        $this->updatePaymentInterval($queries, $codes);

        $codes = [
            PaymentOption::NONE,
            PaymentOption::BANKTRANSFER,
            PaymentOption::CASH,
            PaymentOption::CREDITCARD,
            PaymentOption::SEPA_DIRECT_DEBIT,
        ];
        $this->updatePaymentOption($queries, $codes);



//         $table = $schema->getTable('dmkclub_member_proposal');
//         if ($table->hasColumn('status')) {
//             $table->dropColumn('status');
//         }
    }

    /**
     * @param QueryBag $queries
     * @param array $codes
     */
    protected function updatePaymentInterval($queries, $codes)
    {
        $query = 'UPDATE dmkclub_member_proposal SET payment_interval_id = :code_id WHERE payment_interval = :code_name';
        foreach ($codes as $code) {
            $migrationQuery = new ParametrizedSqlMigrationQuery();
            $migrationQuery->addSql(
                $query,
                ['code_id' => $code, 'code_name' => $code],
                ['code_id' => Type::STRING, 'code_name' => Type::INTEGER]
                );
            $queries->addPostQuery($migrationQuery);
        }
    }

    /**
     * @param QueryBag $queries
     * @param array $codes
     */
    protected function updatePaymentOption($queries, $codes)
    {
        $query = 'UPDATE dmkclub_member_proposal SET payment_option_id = :code_id WHERE payment_option = :code_name';
        foreach ($codes as $code) {
            $migrationQuery = new ParametrizedSqlMigrationQuery();
            $migrationQuery->addSql(
                $query,
                ['code_id' => $code, 'code_name' => $code],
                ['code_id' => Type::STRING, 'code_name' => Type::STRING]
                );
            $queries->addPostQuery($migrationQuery);
        }
        $query = 'UPDATE dmkclub_member SET payment_option_id = :code_id WHERE payment_option = :code_name';
        foreach ($codes as $code) {
            $migrationQuery = new ParametrizedSqlMigrationQuery();
            $migrationQuery->addSql(
                $query,
                ['code_id' => $code, 'code_name' => $code],
                ['code_id' => Type::STRING, 'code_name' => Type::STRING]
                );
            $queries->addPostQuery($migrationQuery);
        }
    }
}
