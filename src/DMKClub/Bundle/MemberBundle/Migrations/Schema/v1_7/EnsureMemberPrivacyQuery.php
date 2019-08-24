<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5;

use Psr\Log\LoggerInterface;

use Oro\Bundle\MigrationBundle\Migration\ArrayLogger;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;

class EnsureMemberPrivacyQuery extends ParametrizedMigrationQuery
{

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $logger = new ArrayLogger();
        $logger->info(
            'Ensure member privacy entity in database.'
        );
        $this->doExecute($logger, true);

        return $logger->getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(LoggerInterface $logger)
    {
        $this->doExecute($logger);
    }

    /**
     * @param LoggerInterface $logger
     * @param bool            $dryRun
     */
    public function doExecute(LoggerInterface $logger, $dryRun = false)
    {
        $sql = 'INSERT INTO dmkclub_member_privacy (member, created_at, updated_at)
SELECT m.id, NOW(), NOW()
FROM dmkclub_member m LEFT JOIN dmkclub_member_privacy mp ON m.id = mp.member WHERE mp.member IS NULL
';

        $this->logQuery($logger, $sql);
        if (!$dryRun) {
            $this->connection->executeUpdate($sql);
        }

    }
}
