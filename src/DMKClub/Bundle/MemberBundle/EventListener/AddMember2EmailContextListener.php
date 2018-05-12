<?php

namespace DMKClub\Bundle\MemberBundle\EventListener;


use Psr\Log\LoggerInterface;

use Oro\Bundle\ActivityBundle\Event\ActivityEvent;
use Oro\Bundle\EmailBundle\Entity\Email;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Oro\Bundle\EmailBundle\Entity\Manager\EmailActivityManager;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;


class AddMember2EmailContextListener
{
    /** @var EmailActivityManager */
    private $emailActivityManager;
    /** @var MemberManager */
    private $mbrManager;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EmailActivityManager $emailActivityManager,
        MemberManager $memberManager,
        LoggerInterface $logger
        )
    {
        $this->emailActivityManager = $emailActivityManager;
        $this->mbrManager = $memberManager;
        $this->logger = $logger;
    }
    /**
     * Add member to email context each time a contact entity is added.
     *
     * @param ActivityEvent $event
     *
     */
    public function addMember2EmailContext(ActivityEvent $event)
    {
        /* @var $emailActivity Email */
        $emailActivity = $event->getActivity();
        $target = $event->getTarget();
        if($emailActivity instanceof Email && $target instanceof Contact) {
            if($member = $this->mbrManager->findMemberByContact($target)) {
                $this->logger->notice(sprintf(
                    'Add association from email %s (%d) to member %s (%d)',
                    $emailActivity->getSubject(),
                    $emailActivity->getId(),
                    $member->getName(),
                    $member->getId()));
                $this->emailActivityManager->addAssociation($emailActivity, $member);
            }
        }
    }
}