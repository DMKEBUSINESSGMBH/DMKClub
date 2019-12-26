<?php
namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Form\Type\Select2EntityType;

class TwigTemplateSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'dmkclub_twigtemplates',
                'class' => 'DMKClubBasicsBundle:TwigTemplate',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
        		'configs'            => [
                    'allowClear' => true,
            		'placeholder' => 'dmkclub.form.choose'
                ],
                'empty_value' => '',
                'empty_data'  => null
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2EntityType::class;
    }
}
