<?php
namespace DMKClub\Bundle\PaymentBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;

class DMKClubPaymentBundleInstaller implements Installation, ExtendExtensionAwareInterface
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getMigrationVersion()
    {
        return 'v1_1';
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /**
         * Tables generation *
         */
        $this->createDmkclubBankaccountTable($schema);
        $this->createDmkclubSepacreditorTable($schema);

        $this->addDmkclubSepacreditorForeignKeys($schema);

        self::addPaymentIntervalEnum($schema, $queries, $this->extendExtension);
        self::addPaymentOptionEnum($schema, $queries, $this->extendExtension);
    }

    /**
     * Create dmkclub_bankaccount table
     *
     * @param Schema $schema
     */
    protected function createDmkclubBankaccountTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_bankaccount');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('account_owner', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('iban', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('bic', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('bank_name', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('created', 'datetime', []);
        $table->addColumn('updated', 'datetime', []);
        $table->addColumn('direct_debit_valid_from', 'date', [
            'notnull' => false
        ]);
        $table->setPrimaryKey([
            'id'
        ]);
    }

    /**
     * Create dmkclub_sepacreditor table
     *
     * @param Schema $schema
     */
    protected function createDmkclubSepacreditorTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_sepacreditor');
        $table->addColumn('id', 'integer', [
            'autoincrement' => true
        ]);
        $table->addColumn('organization_id', 'integer', [
            'notnull' => false
        ]);
        $table->addColumn('user_owner_id', 'integer', [
            'notnull' => false
        ]);
        $table->addColumn('name', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('iban', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('bic', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('creditor_id', 'string', [
            'notnull' => false,
            'length' => 255
        ]);
        $table->addColumn('created', 'datetime', []);
        $table->addColumn('updated', 'datetime', []);
        $table->setPrimaryKey([
            'id'
        ]);
        $table->addIndex([
            'user_owner_id'
        ], 'IDX_77AFE1949EB185F9', []);
        $table->addIndex([
            'organization_id'
        ], 'IDX_77AFE19432C8A3DE', []);
    }

    /**
     * Add dmkclub_sepacreditor foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubSepacreditorForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_sepacreditor');
        $table->addForeignKeyConstraint($schema->getTable('oro_organization'), [
            'organization_id'
        ], [
            'id'
        ], [
            'onDelete' => 'SET NULL',
            'onUpdate' => null
        ]);
        $table->addForeignKeyConstraint($schema->getTable('oro_user'), [
            'user_owner_id'
        ], [
            'id'
        ], [
            'onDelete' => 'SET NULL',
            'onUpdate' => null
        ]);
    }

    /**
     *
     * @param Schema $schema
     * @param ExtendExtension $extendExtension
     * @param array $immutableCodes
     */
    public static function addPaymentIntervalEnum(Schema $schema, QueryBag $queries, ExtendExtension $extendExtension)
    {
        $immutableCodes = [
            PaymentInterval::YEARLY,
            PaymentInterval::HALF_YEARLY,
            PaymentInterval::QUARTERLY,
            PaymentInterval::MONTHLY
        ];
        $codes = [
            PaymentInterval::YEARLY => 'yearly',
            PaymentInterval::HALF_YEARLY => 'half-yearly',
            PaymentInterval::QUARTERLY => 'quarterly',
            PaymentInterval::MONTHLY => 'monthly'
        ];

        $enumCode = PaymentInterval::INTERNAL_ENUM_CODE;
        $enumTable = $extendExtension->createEnum($schema, $enumCode, false, true);


        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', $immutableCodes);

        $enumTable->addOption(OroOptions::KEY, $options);
        self::addEnumValues($queries, $enumTable->getName(), $codes, PaymentInterval::YEARLY);
    }

    /**
     *
     * @param Schema $schema
     * @param ExtendExtension $extendExtension
     * @param array $immutableCodes
     */
    public static function addPaymentOptionEnum(Schema $schema, QueryBag $queries, ExtendExtension $extendExtension)
    {
        $immutableCodes = [
            PaymentOption::NONE,
        ];
        $codes = [
            PaymentOption::NONE => 'None',
            PaymentOption::SEPA_DIRECT_DEBIT => 'SEPA direct debit',
            PaymentOption::BANKTRANSFER => 'Bank transfer',
            PaymentOption::CREDITCARD => 'Credit card',
            PaymentOption::CASH => 'Cash',
        ];

        $enumCode = PaymentOption::INTERNAL_ENUM_CODE;
        $enumTable = $extendExtension->createEnum($schema, $enumCode, false, true);


        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', $immutableCodes);

        $enumTable->addOption(OroOptions::KEY, $options);
        self::addEnumValues($queries, $enumTable->getName(), $codes, PaymentOption::NONE);
    }

    protected static function addEnumValues(QueryBag $queries, $enumTable, array $codes, $defaultValue = 'initial')
    {
        $query = 'INSERT INTO '.$enumTable.' (id, name, priority, is_default)
                  VALUES (:id, :name, :priority, :is_default)';
        $i = 1;
        foreach ($codes as $key => $value) {
            $dropFieldsQuery = new ParametrizedSqlMigrationQuery();
            $dropFieldsQuery->addSql($query, [
                'id' => $key,
                'name' => $value,
                'priority' => $i,
                'is_default' => $defaultValue === $key
            ], [
                'id' => Type::STRING,
                'name' => Type::STRING,
                'priority' => Type::INTEGER,
                'is_default' => Type::BOOLEAN
            ]);
            $queries->addQuery($dropFieldsQuery);
            $i ++;
        }
    }

    /**
     * @param ExtendExtension $extendExtension
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }
}
