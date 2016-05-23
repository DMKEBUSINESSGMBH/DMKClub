<?php
namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TwigTemplateSelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'genemu_jqueryselect2_translatable_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dmkclub_basics_twigtemplate_select';
    }
}
