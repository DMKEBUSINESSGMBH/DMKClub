<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;

class AddMemberProposal implements Migration {

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
		DMKClubMemberBundleInstaller::createDmkclubMemberProposalTable($schema);

		DMKClubMemberBundleInstaller::addDmkclubMemberProposalForeignKeys($schema);
	}
}