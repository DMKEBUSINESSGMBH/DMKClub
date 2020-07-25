<?php
namespace DMKClub\Bundle\MemberBundle\Command;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Mailer\Processor;
use DMKClub\Bundle\BasicsBundle\Model\TemplateNotFoundException;
use Symfony\Component\VarDumper\VarDumper;
use Psr\Log\LoggerInterface;

/**
 * Send membership fees to member by email.
 */
class SendFeeMailsCommand extends ContainerAwareCommand
{

    const NAME = 'dmkclub:send:fee';

    const FLUSH_BATCH_SIZE = 100;

    protected $output;

    /**
     *
     * @return ConfigManager
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Command to send a number of fees by email to member')
            ->addArgument('ids', InputArgument::REQUIRED, 'commaseperated id of fees to send');
    }

    /**
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     *
     * @return Processor
     */
    protected function getMailer()
    {
        return $this->getContainer()->get(Processor::class);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initLanguage();

        $this->output = $output;
        $ids = $input->getArgument('ids');
        try {
            $result = $this->processEmails(explode(',', $ids));
            $output->writeln(sprintf('<info>Job finished</info> with %d processed fees and %d errors', $result['cnt'], $result['errors']));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Job failed</error>'));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    protected function processEmails($feeIds)
    {
        $iteration = 0;
        $errorCnt = 0;
        foreach ($feeIds as $feeId) {
            $iteration ++;
            $entity = $this->getEntityManager()
                ->getRepository(MemberFee::class)
                ->find($feeId);
            try {
                // SEND EMAIL
                $this->getMailer()->sendBillToMemberEmail($entity);
            } catch (TemplateNotFoundException $tnfe) {
                $this->output->writeln($tnfe->getMessage());
                $this->log($tnfe->getMessage());
                // Ohne Template wird keine Mail verschickt werden.
                throw $tnfe;
            } catch (\Exception $e) {
                $msg = sprintf('Fee mailing failed for member %s (%s), fee-id: %d, Error: %s', $entity->getMember()
                    ->getName(), $entity->getMember()
                    ->getId(), $entity->getId(), $e->getMessage());
                $this->log($msg);
                $this->output->writeln($msg);
                // Diese Exception wird nicht weitergegeben, damit der Rest versendet wird
                $errorCnt += 1;
            }

            if (($iteration % self::FLUSH_BATCH_SIZE) === 0) {
                $this->getEntityManager()->flush();
                $this->getEntityManager()->clear();
            }
        }
        return [
            'cnt' => $iteration,
            'errors' => $errorCnt
        ];
    }

    protected function log($msg)
    {
        /* @var $logger LoggerInterface */
        $logger = $this->getContainer()->get('logger');
        $logger->critical($msg);
    }

    protected function initLanguage()
    {
        /* @var \Oro\Bundle\ConfigBundle\Config\ConfigManager */
        $configManager = $this->getContainer()->get('oro_config.global');
        $t = $this->getContainer()->get('translator');
//        $t->setLocale($configManager->get('oro_locale.language'));
    }
}
