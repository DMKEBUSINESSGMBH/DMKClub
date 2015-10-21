<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_4;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;

class AddBankAccount implements Migration {

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		$table = $schema->getTable('dmkclub_member');
		$table->addColumn('bank_account', 'integer', ['notnull' => false]);
		$table->addIndex(['bank_account'], 'IDX_6A79FCCD53A23E0A', []);
		$table->addForeignKeyConstraint(
				$schema->getTable('dmkclub_bankaccount'),
				['bank_account'],
				['id'],
				['onDelete' => 'SET NULL', 'onUpdate' => null]
		);

	}

}
