<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubMemberBundleInstaller implements Installation, ActivityExtensionAwareInterface, CommentExtensionAwareInterface
{
    /** @var CommentExtension */
    protected $comment;

    /** @var ActivityExtension */
    protected $activityExtension;

    /**
     * @param CommentExtension $commentExtension
     */
    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_4';
    }

    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createDmkclubFeecategoryTable($schema);
    	  $this->createDmkclubMemberTable($schema);

        /** Foreign keys generation **/
        $this->addDmkclubFeecategoryForeignKeys($schema);
    		$this->addDmkclubMemberForeignKeys($schema);

        $this->comment->addCommentAssociation($schema, 'dmkclub_member');
    }

    /**
     * Create dmkclub_feecategory table
     *
     * @param Schema $schema
     */
    protected function createDmkclubFeecategoryTable(Schema $schema)
    {
    	$table = $schema->createTable('dmkclub_feecategory');
    	$table->addColumn('id', 'integer', ['autoincrement' => true]);
    	$table->addColumn('organization_id', 'integer', ['notnull' => false]);
    	$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
    	$table->addColumn('name', 'string', ['length' => 255]);
    	$table->addColumn('created_at', 'datetime', []);
    	$table->addColumn('updated_at', 'datetime', []);
    	$table->setPrimaryKey(['id']);
    	$table->addIndex(['user_owner_id'], 'IDX_291829209EB185F9', []);
    	$table->addIndex(['organization_id'], 'IDX_2918292032C8A3DE', []);
    }

    /**
     * Create dmkclub_member table
     *
     * @param Schema $schema
     */
    protected function createDmkclubMemberTable(Schema $schema)
    {
    	$table = $schema->createTable('dmkclub_member');
    	$table->addColumn('id', 'integer', ['autoincrement' => true]);
      $table->addColumn('fee_category', 'integer', ['notnull' => false]);
    	$table->addColumn('bank_account', 'integer', ['notnull' => false]);
    	$table->addColumn('organization_id', 'integer', ['notnull' => false]);
    	$table->addColumn('postal_address', 'integer', ['notnull' => false]);
    	$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
    	$table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
    	$table->addColumn('account_id', 'integer', ['notnull' => false]);
    	$table->addColumn('contact_id', 'integer', ['notnull' => false]);
    	$table->addColumn('member_code', 'string', ['notnull' => false, 'length' => 255]);
    	$table->addColumn('start_date', 'date', ['notnull' => false]);
    	$table->addColumn('end_date', 'date', ['notnull' => false]);
    	$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
    	$table->addColumn('created_at', 'datetime', []);
    	$table->addColumn('updated_at', 'datetime', []);
    	$table->addColumn('is_active', 'boolean', ['default' => '0']);
    	$table->addColumn('status', 'string', ['default' => 'active', 'notnull' => false, 'length' => 20]);
    	$table->addColumn('payment_option', 'string', ['default' => 'none', 'notnull' => false, 'length' => 20]);
    	$table->addColumn('is_honorary', 'boolean', ['default' => '0']);
    	$table->addColumn('is_free_of_charge', 'boolean', ['default' => '0']);
    	$table->setPrimaryKey(['id']);
    	$table->addIndex(['contact_id'], 'IDX_6A79FCCDE7A1254A', []);
    	$table->addIndex(['postal_address'], 'IDX_6A79FCCD972EFBF7', []);
    	$table->addIndex(['user_owner_id'], 'IDX_6A79FCCD9EB185F9', []);
    	$table->addIndex(['organization_id'], 'IDX_6A79FCCD32C8A3DE', []);
    	$table->addIndex(['account_id'], 'IDX_6A79FCCD9B6B5FBA', []);
    	$table->addIndex(['data_channel_id'], 'IDX_6A79FCCDBDC09B73', []);
    	$table->addIndex(['bank_account'], 'IDX_6A79FCCD53A23E0A', []);
    }

    /**
     * Add dmkclub_feecategory foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubFeecategoryForeignKeys(Schema $schema)
    {
    	$table = $schema->getTable('dmkclub_feecategory');
    	$table->addForeignKeyConstraint(
    			$schema->getTable('oro_organization'),
    			['organization_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('oro_user'),
    			['user_owner_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    }

    /**
     * Add dmkclub_member foreign keys.
     *
     * @param Schema $schema
     */
    protected function addDmkclubMemberForeignKeys(Schema $schema)
    {
    	$table = $schema->getTable('dmkclub_member');
      $table->addForeignKeyConstraint(
          $schema->getTable('dmkclub_feecategory'),
          ['fee_category'],
          ['id'],
          ['onDelete' => 'SET NULL', 'onUpdate' => null]
      );
    	$table->addForeignKeyConstraint(
    			$schema->getTable('dmkclub_bankaccount'),
    			['bank_account'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('oro_organization'),
    			['organization_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('oro_address'),
    			['postal_address'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('oro_user'),
    			['user_owner_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('orocrm_channel'),
    			['data_channel_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('orocrm_account'),
    			['account_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    	$table->addForeignKeyConstraint(
    			$schema->getTable('orocrm_contact'),
    			['contact_id'],
    			['id'],
    			['onDelete' => 'SET NULL', 'onUpdate' => null]
    	);
    }

}
