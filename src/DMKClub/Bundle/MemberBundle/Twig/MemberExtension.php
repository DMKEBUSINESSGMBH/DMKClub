<?php

namespace DMKClub\Bundle\MemberBundle\Twig;

use DMKClub\Bundle\MemberBundle\Provider\MemberStatusProvider;
use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use Oro\Bundle\ContactBundle\Entity\Contact;

class MemberExtension extends \Twig_Extension
{
    /**
     * @var MemberStatusProvider
     */
    protected $statusProvider;

    /** @var MemberManager */
    protected $mbrManager;

    /**
     * @param MemberStatusProvider $statusProvider
     * @param MemberManager $mbrManager
     */
    public function __construct(MemberStatusProvider $statusProvider, MemberManager $mbrManager)
    {
        $this->statusProvider = $statusProvider;
        $this->mbrManager = $mbrManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array an array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('dmkclub_memberstatus', [$this, 'getMemberStatusLabel']),
            new \Twig\TwigFunction('dmkclub_memberByContact', [$this, 'getMemberByContact']),
        ];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getMemberStatusLabel($name)
    {
        if (!$name) {
            return null;
        }

        return $this->statusProvider->getLabelByName($name);
    }

    /**
     * @param Contact $contact
     * @return Member|null
     */
    public function getMemberByContact(Contact $contact)
    {
        return $this->mbrManager->findMemberByContact($contact);
    }


    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_member';
    }
}
