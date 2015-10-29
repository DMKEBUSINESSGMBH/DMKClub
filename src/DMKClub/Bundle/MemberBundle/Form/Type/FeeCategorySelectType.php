<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeeCategorySelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'feecategories', // Der Alias wird vom search_handler verwendet
                'create_form_route'  => 'dmkclub_feecategory_create',
                'configs'            => [
        							# Das ist nur ein Label
                    'placeholder' => 'dmkclub.member.form.choose_feecategory'
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'oro_entity_create_or_select_inline';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dmkclub_feecategory_select';
    }
}
