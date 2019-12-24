<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\UserBundle\Provider\GenderProvider;
use DMKClub\Bundle\MemberBundle\Provider\MemberStatusProvider;

class MemberStatusType extends AbstractType
{
    /**
     * @var MemberStatusProvider
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
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
