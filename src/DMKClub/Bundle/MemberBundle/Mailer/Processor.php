<?php

namespace DMKClub\Bundle\MemberBundle\Mailer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Gaufrette\File;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EmailBundle\Provider\EmailRenderer;
use Oro\Bundle\EmailBundle\Tools\EmailHolderHelper;

use DMKClub\Bundle\BasicsBundle\Model\Attachment;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\MemberBundle\Event\CreatePdf4EmailEvent;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;

class Processor extends BaseProcessor
{
    const TEMPLATE_FEE_TO_MEMBER    = 'dmkclub_fee_to_member';
    /** @var Manager */
    protected $pdfManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ManagerRegistry $managerRegistry,
        ConfigManager $configManager,
        EmailRenderer $renderer,
        EmailHolderHelper $emailHolderHelper,
        \Oro\Bundle\EmailBundle\Mailer\Processor $mailer,
        Manager $pdfManager)
    {
        parent::__construct($eventDispatcher, $managerRegistry, $configManager, $renderer, $emailHolderHelper, $mailer);
        $this->pdfManager = $pdfManager;
    }
    /**
     * @param MemberFee $fee
     *
     * @return int number of emails sent
     */
    public function sendBillToMemberEmail(MemberFee $fee)
    {
        $member = $fee->getMember();
        // Create PDF
        $file = $this->buildFeePdf($fee);
        $event = new CreatePdf4EmailEvent($file);
        $this->eventDispatcher->dispatch(CreatePdf4EmailEvent::EVENT_NAME, $event);
        $file = $event->getPdfFile();

        $attachment = new Attachment($file);
        $file->delete();

        return $this->getEmailTemplateAndSendEmail(
            $member,
            static::TEMPLATE_FEE_TO_MEMBER,
            ['entity' => $fee],
            [$attachment]
        );
    }

    /**
     *
     * @param MemberFee $fee
     * @return File
     */
    protected function buildFeePdf(MemberFee $fee):File
    {
        return $this->pdfManager->buildPdf($fee);
    }
}
