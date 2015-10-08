<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;

class DMKClubMemberBundle implements Migration
{

	/**
	 * OptionField für Mitgliedsstatus:
	 * - Antragssteller
	 * - aktives Mitglied
	 * - Ex-Mitglied
	 *
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
		$table = $schema->getTable('dmkclub_member');
		$table->addColumn('status', 'string', ['notnull' => false, 'length' => 8]);
	}


}
