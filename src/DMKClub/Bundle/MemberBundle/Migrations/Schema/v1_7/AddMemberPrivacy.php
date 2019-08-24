<?php
namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_7;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5\EnsureMemberPrivacyQuery;

class AddMemberPrivacy implements Migration
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createDmkclubMemberPrivacyTable($schema);
        $this->addDmkclubMemberPrivacyForeignKeys($schema);

        $queries->addPostQuery(new EnsureMemberPrivacyQuery());
    }

    /**
     * Create dmkclub_member_privacy table
     *
     * @param Schema $schema
     */
    protected function createDmkclubMemberPrivacyTable(Schema $schema)
    {
        $table = $schema->createTable('dmkclub_member_privacy');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('member', 'integer', ['notnull' => false]);
        $table->addColumn('sign_date', 'date', ['notnull' => false, 'comment' => '(DC2Type:date)']);
        $table->addColumn('phone_allowed', 'boolean', ['default' => false]);
        $table->addColumn('email_allowed', 'boolean', ['default' => false]);
        $table->addColumn('postal_allowed', 'boolean', ['default' => false]);
        $table->addColumn('merchandising_allowed', 'boolean', ['default' => false]);
        $table->addColumn('sharing_allowed', 'boolean', ['default' => false]);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addUniqueIndex(['member'], 'uniq_a1ec9db70e4fa78');
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add dmkclub_member_privacy foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubMemberPrivacyForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmkclub_member_privacy');
        $table->addForeignKeyConstraint(
            $schema->getTable('dmkclub_member'),
            ['member'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
    }
}
