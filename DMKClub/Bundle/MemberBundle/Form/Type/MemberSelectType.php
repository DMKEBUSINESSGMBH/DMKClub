<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'members',
                'create_form_route'  => 'dmkclub_member_create',
                'configs'            => [
                    'placeholder' => 'dmkclub.form.choose_member'
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'oro_entity_create_or_select_inline_channel_aware';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dmkclub_member_member_select';
    }
}
