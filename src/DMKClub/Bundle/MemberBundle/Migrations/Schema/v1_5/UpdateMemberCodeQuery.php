<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_5;

use Psr\Log\LoggerInterface;

use Oro\Bundle\MigrationBundle\Migration\ArrayLogger;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;

class UpdateMemberCodeQuery extends ParametrizedMigrationQuery
{

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $logger = new ArrayLogger();
        $logger->info(
            'Update member code in integer field.'
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
        $sql = $this->isMysql() ?
            'UPDATE dmkclub_member SET member_code_int = member_code;'
                :
            'UPDATE dmkclub_member SET member_code_int = member_code::integer WHERE member_code ~ E\'^\\\\d+$\';';

        $this->logQuery($logger, $sql);
        if (!$dryRun) {
            $this->connection->executeUpdate($sql);
        }

    }
    protected function isMysql() {
        return $this->connection->getDatabasePlatform()->getName() == 'mysql';
    }

}
