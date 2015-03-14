<?php
namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategorySelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'sponsorcategories', // Der Alias wird vom search_handler verwendet
                'create_form_route'  => 'dmkclub_sponsorcategory_create',
                'configs'            => [
        							# Das ist nur ein Label
                    'placeholder' => 'dmkclub.sponsor.form.choose_category'
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
        return 'dmkclub_sponsorcategory_select';
    }
}
