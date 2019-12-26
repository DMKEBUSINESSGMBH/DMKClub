<?php

namespace DMKClub\Bundle\MemberBundle\Mailer;

use Doctrine\Common\Persistence\ManagerRegistry;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EmailBundle\Model\EmailTemplateInterface;
use Oro\Bundle\EmailBundle\Provider\EmailRenderer;
use Oro\Bundle\EmailBundle\Tools\EmailHolderHelper;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EmailBundle\Mailer\Processor;
use Oro\Bundle\EmailBundle\Form\Model\Email As EmailModel;
use Oro\Bundle\EmailBundle\Entity\EmailAttachment;
use Oro\Bundle\EmailBundle\Entity\EmailAttachmentContent;
use Oro\Bundle\EmailBundle\Form\Model\EmailAttachment As EmailFormAttachment;
use Oro\Bundle\UserBundle\Entity\UserInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use DMKClub\Bundle\BasicsBundle\Model\TemplateNotFoundException;

/**
 * Ggf. ins BasicsBundle verschieben
 */
class BaseProcessor
{
    /** @var ManagerRegistry */
    protected $managerRegistry;

    /** @var ConfigManager */
    protected $configManager;

    /** @var EmailRenderer */
    protected $renderer;

    /** @var EmailHolderHelper */
    protected $emailHolderHelper;

    /** @var Processor */
    protected $mailer;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ManagerRegistry $managerRegistry
     * @param ConfigManager $configManager
     * @param EmailRenderer $renderer
     * @param EmailHolderHelper $emailHolderHelper
     * @param \Swift_Mailer $mailer
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ManagerRegistry $managerRegistry,
        ConfigManager $configManager,
        EmailRenderer $renderer,
        EmailHolderHelper $emailHolderHelper,
        Processor $mailer
    ) {
        $this->eventDispatcher   = $eventDispatcher;
        $this->managerRegistry   = $managerRegistry;
        $this->configManager     = $configManager;
        $this->renderer          = $renderer;
        $this->emailHolderHelper = $emailHolderHelper;
        $this->mailer            = $mailer;
    }

    /**
     * @param UserInterface $object
     * @param array         $templateData
     * @param string        $type
     *
     * @return int          The return value is the number of recipients who were accepted for delivery
     */
    protected function sendEmail(EmailHolderInterface $object, array $templateData, $type, $attachments = [])
    {
        list($subjectRendered, $templateRendered) = $templateData;

        $senderEmail = $this->configManager->get('oro_notification.email_notification_sender_email');
        $senderName  = $this->configManager->get('oro_notification.email_notification_sender_name');

        $toEmail = $this->emailHolderHelper->getEmail($object);
        $toName = $toEmail; // FIXME

        $emailModel = new EmailModel();
        $emailModel
            ->setTo([$this->buildEmailAddressString($toEmail, $toName)])
            ->setFrom($this->buildEmailAddressString($senderEmail, $senderName))
            ->setSubject($subjectRendered)
            ->setBody($templateRendered)
            ->setType($type)
            ->setContexts([$object]);
        ;

        foreach ($attachments as $attachmentData) {
            /* @var $attachmentData \DMKClub\Bundle\BasicsBundle\Model\Attachment */
            $attachment = new EmailAttachment();
            $attachment->setFileName(basename($attachmentData->getFileName()));
            $content = new EmailAttachmentContent();
            $content->setContent(base64_encode($attachmentData->getContent()));
            $content->setContentTransferEncoding('base64'); // Das geht vermutlich besser...
            $attachment->setContent($content);
            $attachment->setContentType($attachmentData->getContentType());

            $formAttachment = new EmailFormAttachment();
            $formAttachment->setEmailAttachment($attachment);
            $emailModel->addAttachment($formAttachment);
        }
        return $this->mailer->process($emailModel);
    }

    protected function buildEmailAddressString($email, $name)
    {
        return $name ? sprintf('%s <%s>', $name, $email) : $email;
    }

    /**
     * @param string $emailTemplateName
     *
     * @return null|EmailTemplateInterface
     */
    protected function findEmailTemplateByName($emailTemplateName)
    {
        return $this->managerRegistry
            ->getManagerForClass('OroEmailBundle:EmailTemplate')
            ->getRepository('OroEmailBundle:EmailTemplate')
            ->findByName($emailTemplateName);
    }

    /**
     * @param UserInterface $emailHolder
     * @param string        $emailTemplateName
     * @param array         $emailTemplateParams
     *
     * @return int
     */
    public function getEmailTemplateAndSendEmail(
        EmailHolderInterface $emailHolder,
        $emailTemplateName,
        array $emailTemplateParams = [],
        array $attachments = []
    ) {
        $emailTemplate = $this->findEmailTemplateByName($emailTemplateName);
        if (!$emailTemplate) {
            throw new TemplateNotFoundException(sprintf('No email template "%s" found.', $emailTemplateName));
        }

        return $this->sendEmail(
            $emailHolder,
            $this->renderer->compileMessage($emailTemplate, $emailTemplateParams),
            $emailTemplate->getType(),
            $attachments
        );
    }

}
