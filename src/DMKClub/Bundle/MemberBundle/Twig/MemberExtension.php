<?php

namespace DMKClub\Bundle\MemberBundle\Twig;

use DMKClub\Bundle\MemberBundle\Provider\MemberStatusProvider;

class MemberExtension extends \Twig_Extension
{
    /**
     * @var MemberStatusProvider
     */
    protected $statusProvider;

    /**
     * @param MemberStatusProvider $statusProvider
     */
    public function __construct(MemberStatusProvider $statusProvider)
    {
        $this->statusProvider = $statusProvider;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'dmkclub_memberstatus' => new \Twig_Function_Method($this, 'getMemberStatusLabel'),
        );
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
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_member';
    }
}
