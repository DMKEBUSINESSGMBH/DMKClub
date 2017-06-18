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
	    return 'v1_2';
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
	    self::createDmkclubMemberProposalBankaccountTable($schema);
	    self::createDmkclubMemberProposalAddressTable($schema);
	    self::createDmkclubMemberProposalTable($schema);
	    $this->createDmkclubMemberFeediscountTable($schema);
	    $this->createDmkclubMemberFeeTable($schema);
	    $this->createDmkclubMemberTable($schema);
		$this->createDmkclubMemberBillingTable($schema);
		$this->createDmkclubMemberFeepositionTable($schema);

		/** Foreign keys generation **/
		self::addDmkclubMemberProposalAddressForeignKeys($schema);
		self::addDmkclubMemberProposalForeignKeys($schema);
		$this->addDmkclubMemberFeediscountForeignKeys($schema);
		$this->addDmkclubMemberFeeForeignKeys($schema);
		$this->addDmkclubMemberForeignKeys($schema);
		$this->addDmkclubMemberBillingForeignKeys($schema);
		$this->addDmkclubMemberFeepositionForeignKeys($schema);

		$this->comment->addCommentAssociation($schema, 'dmkclub_member');
		$this->comment->addCommentAssociation($schema, 'dmkclub_member_proposal');
		self::addActivityAssociations($schema, $this->activityExtension);
		self::addActivityAssociations4Proposal($schema, $this->activityExtension);
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
	 * Create dmkclub_member_billing table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberBillingTable(Schema $schema) {
		$table = $schema->createTable('dmkclub_member_billing');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('creditor_id', 'integer', ['notnull' => false]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('template_id', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('segment_id', 'integer', ['notnull' => false]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->addColumn('processor', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('processor_config', 'text', ['notnull' => false]);
		$table->addColumn('fee_total', 'integer', ['notnull' => false]);
		$table->addColumn('export_filesystem', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('position_labels', 'text', ['notnull' => false]);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['user_owner_id'], 'IDX_25B89C799EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_25B89C7932C8A3DE', []);
		$table->addIndex(['segment_id'], 'IDX_25B89C79DB296AAD', []);
		$table->addIndex(['template_id'], 'IDX_25B89C795DA0FB8', []);
		$table->addIndex(['creditor_id'], 'IDX_25B89C79DF91AC92', []);
	}

	/**
	 * Create dmkclub_member_fee table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberFeeTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member_fee');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('member', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('billing', 'integer', ['notnull' => false]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('bill_date', 'date', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('price_total', 'integer', ['notnull' => false]);
		$table->addColumn('payed_total', 'integer', ['notnull' => false]);
		$table->addColumn('correction_status', 'integer', ['notnull' => false]);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['billing'], 'IDX_B0418BD9EC224CAA', []);
		$table->addIndex(['member'], 'IDX_B0418BD970E4FA78', []);
		$table->addIndex(['user_owner_id'], 'IDX_B0418BD99EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_B0418BD932C8A3DE', []);
	}
	/**
	 * Create dmkclub_member_feediscount table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberFeediscountTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member_feediscount');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('member', 'integer', ['notnull' => false]);
		$table->addColumn('start_date', 'date', ['notnull' => false]);
		$table->addColumn('end_date', 'date', ['notnull' => false]);
		$table->addColumn('reason', 'string', ['notnull' => false, 'length' => 255]);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['member'], 'IDX_A660B6EC70E4FA78', []);
	}

	/**
	 * Create dmkclub_member_feeposition table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubMemberFeepositionTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_member_feeposition');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('member_fee', 'integer', ['notnull' => false]);
		$table->addColumn('quantity', 'float', ['notnull' => false]);
		$table->addColumn('unit', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('flag', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('description', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('price_single', 'integer', ['notnull' => false]);
		$table->addColumn('price_total', 'integer', ['notnull' => false]);
		$table->addColumn('tax_amount', 'integer', ['notnull' => false]);
		$table->addColumn('sort_order', 'integer', ['notnull' => false]);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['member_fee'], 'IDX_1ACE617A7ED44EE', []);
	}

	/**
	 * Create dmkclub_member_proposal_bank table
	 *
	 * @param Schema $schema
	 */
	public static function createDmkclubMemberProposalBankaccountTable(Schema $schema)
	{
	    $table = $schema->createTable('dmkclub_member_proposal_bank');
	    $table->addColumn('id', 'integer', ['autoincrement' => true]);
	    $table->addColumn('account_owner', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('iban', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('bic', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('bank_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('direct_debit_valid_from', 'date', ['notnull' => false, 'comment' => '(DC2Type:date)']);
	    $table->addColumn('created', 'datetime', ['comment' => '(DC2Type:datetime)']);
	    $table->addColumn('updated', 'datetime', ['comment' => '(DC2Type:datetime)']);
	    $table->setPrimaryKey(['id']);
	}

	/**
	 * Create dmkclub_member_proposal_addr table
	 *
	 * @param Schema $schema
	 */
	public static function createDmkclubMemberProposalAddressTable(Schema $schema)
	{
	    $table = $schema->createTable('dmkclub_member_proposal_addr');
	    $table->addColumn('id', 'integer', ['autoincrement' => true]);
	    $table->addColumn('region_code', 'string', ['notnull' => false, 'length' => 16]);
	    $table->addColumn('country_code', 'string', ['notnull' => false, 'length' => 2]);
	    $table->addColumn('label', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('street', 'string', ['notnull' => false, 'length' => 500]);
	    $table->addColumn('street2', 'string', ['notnull' => false, 'length' => 500]);
	    $table->addColumn('city', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('postal_code', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('organization', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('region_text', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('name_prefix', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('first_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('middle_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('last_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('name_suffix', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('created', 'datetime', ['comment' => '(DC2Type:datetime)']);
	    $table->addColumn('updated', 'datetime', ['comment' => '(DC2Type:datetime)']);
	    $table->addIndex(['region_code'], 'idx_e223224aeb327af', []);
	    $table->addIndex(['country_code'], 'idx_e223224f026bb7c', []);
	    $table->setPrimaryKey(['id']);
	}

	/**
	 * Create dmkclub_memberproposal table
	 *
	 * @param Schema $schema
	 */
	public static function createDmkclubMemberProposalTable(Schema $schema)
	{
	    $table = $schema->createTable('dmkclub_member_proposal');
	    $table->addColumn('id', 'integer', ['autoincrement' => true]);
	    $table->addColumn('data_channel_id', 'integer', ['notnull' => false]);
	    $table->addColumn('organization_id', 'integer', ['notnull' => false]);
	    $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
	    $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
	    $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
	    $table->addColumn('member_id', 'integer', ['notnull' => false]);
	    $table->addColumn('postal_address', 'integer', ['notnull' => false]);
	    $table->addColumn('bank_account', 'integer', ['notnull' => false]);
	    $table->addColumn('name_prefix', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('first_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('middle_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('last_name', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('name_suffix', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('email_address', 'string', ['notnull' => false, 'length' => 100]);
	    $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 100]);
	    $table->addColumn('birthday', 'date', ['notnull' => false, 'comment' => '(DC2Type:date)']);
	    $table->addColumn('comment', 'text', ['notnull' => false]);
	    $table->addColumn('is_active', 'boolean', ['default' => '0']);
	    $table->addColumn('status', 'string', ['default' => 'active', 'notnull' => false, 'length' => 20]);
	    $table->addColumn('payment_option', 'string', ['default' => 'none', 'notnull' => false, 'length' => 20]);
	    $table->addColumn('payment_interval', 'integer', ['default' => '12', 'notnull' => true]);
	    $table->addColumn('job_title', 'string', ['notnull' => false, 'length' => 255]);
	    $table->addColumn('createdat', 'datetime', ['comment' => '(DC2Type:datetime)']);
	    $table->addColumn('updatedat', 'datetime', ['notnull' => false, 'comment' => '(DC2Type:datetime)']);
	    $table->addIndex(['bank_account'], 'idx_a0f68dcc53a23e0a', []);
	    $table->addIndex(['postal_address'], 'idx_a0f68dcc972efbf7', []);
	    $table->addIndex(['data_channel_id'], 'idx_a0f68dccbdc09b73', []);
	    $table->addIndex(['workflow_step_id'], 'idx_a0f68dcc71fe882c', []);
	    $table->addUniqueIndex(['workflow_item_id'], 'uniq_a0f68dcc1023c4ee');
	    $table->setPrimaryKey(['id']);
	    $table->addIndex(['user_owner_id'], 'idx_a0f68dcc9eb185f9', []);
	    $table->addIndex(['member_id'], 'idx_a0f68dcc7597d3fe', []);
	    $table->addIndex(['organization_id'], 'idx_a0f68dcc32c8a3de', []);
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

	/**
	 * Add dmkclub_member_billing foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberBillingForeignKeys(Schema $schema) {
		$table = $schema->getTable('dmkclub_member_billing');
		$table->addForeignKeyConstraint(
			$schema->getTable('dmkclub_sepacreditor'),
			['creditor_id'],
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
				$schema->getTable('dmkclub_basics_twigtemplate'),
				['template_id'],
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
				$schema->getTable('oro_segment'),
				['segment_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}
	/**
	 * Add dmkclub_member_feediscount foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberFeediscountForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_feediscount');
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member'),
				['member'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
	}

	/**
	 * Add dmkclub_member_fee foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberFeeForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_fee');
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member_billing'),
				['billing'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_organization'),
				['organization_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member'),
				['member'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
		$table->addForeignKeyConstraint(
				$schema->getTable('oro_user'),
				['user_owner_id'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);
	}
	/**
	 * Add dmkclub_member_feeposition foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubMemberFeepositionForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_member_feeposition');
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_member_fee'),
				['member_fee'],
				['id'],
				['onDelete' => 'CASCADE', 'onUpdate' => null]
		);
	}

	/**
	 * Add dmkclub_member_proposal_addr foreign keys.
	 *
	 * @param Schema $schema
	 */
	public static function addDmkclubMemberProposalAddressForeignKeys(Schema $schema)
	{
	    $table = $schema->getTable('dmkclub_member_proposal_addr');
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_dictionary_region'),
	        ['region_code'],
	        ['combined_code'],
	        ['onUpdate' => null, 'onDelete' => null]
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_dictionary_country'),
	        ['country_code'],
	        ['iso2_code'],
	        ['onUpdate' => null, 'onDelete' => null]
	    );
	}

	/**
	 * Add dmkclub_memberproposal foreign keys.
	 *
	 * @param Schema $schema
	 */
	public static function addDmkclubMemberProposalForeignKeys(Schema $schema)
	{
	    $table = $schema->getTable('dmkclub_member_proposal');
	    $table->addForeignKeyConstraint(
	        $schema->getTable('orocrm_channel'),
	        ['data_channel_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_organization'),
	        ['organization_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_user'),
	        ['user_owner_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_workflow_step'),
	        ['workflow_step_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('oro_workflow_item'),
	        ['workflow_item_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('dmkclub_member'),
	        ['member_id'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('dmkclub_member_proposal_addr'),
	        ['postal_address'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	    $table->addForeignKeyConstraint(
	        $schema->getTable('dmkclub_member_proposal_bank'),
	        ['bank_account'],
	        ['id'],
	        ['onUpdate' => null, 'onDelete' => 'SET NULL']
	    );
	}

	/**
	 * Enables activities
	 *
	 * @param Schema            $schema
	 * @param ActivityExtension $activityExtension
	 */
	public static function addActivityAssociations(Schema $schema, ActivityExtension $activityExtension) {
		$activityExtension->addActivityAssociation($schema, 'orocrm_call', 'dmkclub_member');
		$activityExtension->addActivityAssociation($schema, 'orocrm_task', 'dmkclub_member');
		$activityExtension->addActivityAssociation($schema, 'oro_calendar_event', 'dmkclub_member');
	}
	public static function addActivityAssociations4Proposal(Schema $schema, ActivityExtension $activityExtension) {
	    $activityExtension->addActivityAssociation($schema, 'orocrm_call', 'dmkclub_member_proposal');
	    $activityExtension->addActivityAssociation($schema, 'orocrm_task', 'dmkclub_member_proposal');
	}

}
