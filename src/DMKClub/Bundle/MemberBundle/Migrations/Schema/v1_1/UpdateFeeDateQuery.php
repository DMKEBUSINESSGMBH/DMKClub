<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_1;

use Psr\Log\LoggerInterface;

use Oro\Bundle\MigrationBundle\Migration\ArrayLogger;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;

class UpdateFeeDateQuery extends ParametrizedMigrationQuery
{

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $logger = new ArrayLogger();
        $logger->info(
            'Update fee date from createdAt field.'
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
        $sql = 'UPDATE dmkclub_member_fee SET fee_date = created_at';

        $this->logQuery($logger, $sql);
        if (!$dryRun) {
            $this->connection->executeUpdate($sql);
        }

    }
}
