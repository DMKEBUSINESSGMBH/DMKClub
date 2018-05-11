<?php

namespace DMKClub\Bundle\MemberBundle\Mailer;

use DMKClub\Bundle\MemberBundle\Entity\MemberFee;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use DMKClub\Bundle\BasicsBundle\Model\Attachment;

class Processor extends BaseProcessor
{
    const TEMPLATE_FEE_TO_MEMBER    = 'dmkclub_fee_to_member';
    /** @var Manager */
    protected $pdfManager;

    public function __construct($managerRegistry, $configManager, $renderer, $emailHolderHelper, $mailer, Manager $pdfManager)
    {
        parent::__construct($managerRegistry, $configManager, $renderer, $emailHolderHelper, $mailer);
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
        $fileName = $this->buildFeePdf($fee);
        // TODO: raise event to allow modify pdf file
        $attachment = new Attachment($fileName);

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
     * @return string path to pdf file
     */
    protected function buildFeePdf(MemberFee $fee)
    {
        return $this->pdfManager->buildPdf($fee);
    }
}
