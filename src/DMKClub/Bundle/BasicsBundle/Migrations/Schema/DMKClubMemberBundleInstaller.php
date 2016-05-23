<?php

namespace DMKClub\Bundle\BasicsBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKClubBasicsBundleInstaller implements Installation
{
	/**
	 * {@inheritdoc}
	 */
	public function getMigrationVersion()
	{
	    return 'v1_0';
	}

	/**
	 * {@inheritdoc}
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
		/** Tables generation **/
		$this->createDmkclubBasicsTwigtemplateTable($schema);

		/** Foreign keys generation **/
		$this->addDmkclubBasicsTwigtemplateForeignKeys($schema);

	}
	/**
	 * Create dmkclub_basics_twigtemplate table
	 *
	 * @param Schema $schema
	 */
	protected function createDmkclubBasicsTwigtemplateTable(Schema $schema)
	{
		$table = $schema->createTable('dmkclub_basics_twigtemplate');
		$table->addColumn('id', 'integer', ['autoincrement' => true]);
		$table->addColumn('organization_id', 'integer', ['notnull' => false]);
		$table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
		$table->addColumn('name', 'string', ['notnull' => false, 'length' => 255]);
		$table->addColumn('template', 'text', []);
		$table->addColumn('created_at', 'datetime', []);
		$table->addColumn('updated_at', 'datetime', []);
		$table->addColumn('orientation', 'string', ['notnull' => false, 'length' => 50]);
		$table->addColumn('page_format', 'text', []);
		$table->setPrimaryKey(['id']);
		$table->addIndex(['user_owner_id'], 'IDX_95E3EB829EB185F9', []);
		$table->addIndex(['organization_id'], 'IDX_95E3EB8232C8A3DE', []);
	}

	/**
	 * Add dmkclub_basics_twigtemplate foreign keys.
	 *
	 * @param Schema $schema
	 */
	protected function addDmkclubBasicsTwigtemplateForeignKeys(Schema $schema)
	{
		$table = $schema->getTable('dmkclub_basics_twigtemplate');
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

}
