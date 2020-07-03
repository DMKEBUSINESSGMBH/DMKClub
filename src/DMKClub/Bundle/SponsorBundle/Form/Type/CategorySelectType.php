<?php
namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;

class CategorySelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'dmkclub_sponsorcategories', // Der Alias wird vom search_handler verwendet
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
        return OroEntitySelectOrCreateInlineType::class;
    }
}
