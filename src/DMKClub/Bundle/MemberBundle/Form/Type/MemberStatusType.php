<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

use Oro\Bundle\UserBundle\Provider\GenderProvider;

class MemberStatusType extends AbstractType
{
    const NAME = 'dmkclub_memberstatus';

    /**
     * @var GenderProvider
     */
    protected $memberStatusProvider;

    /**
     * @param GenderProvider $memberStatusProvider
     */
    public function __construct(MemberStatusProvider $memberStatusProvider)
    {
        $this->memberStatusProvider = $memberStatusProvider;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices'     => $this->memberStatusProvider->getChoices(),
                'multiple'    => false,
                'expanded'    => false,
                'empty_value' => 'dmkclub.member.form.choose_status',
                'translatable_options' => false
            )
        );
    }
}
