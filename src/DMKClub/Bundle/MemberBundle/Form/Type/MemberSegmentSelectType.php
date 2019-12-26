<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Oro\Bundle\FormBundle\Form\Type\Select2EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\VarDumper\VarDumper;
use Doctrine\ORM\QueryBuilder;

/**
 * Class MemberSegmentSelectType
 */
class MemberSegmentSelectType extends AbstractType
{
    const NAME = 'dmkclub_member_segment_select_type';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2EntityType::class;
//        return 'genemu_jqueryselect2_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(
            [
                'label'         => 'oro.segment.entity_label',
                'class'         => 'OroSegmentBundle:Segment',
                'choice_label'  => 'name',
                'random_id'     => true,
                'choices'       => [],
                'configs'       => [
                    'allowClear'  => true,
                    'placeholder' => 'oro.segment.condition_builder.choose_entity_segment'
                ],
                'entities'      => [],
                'translatable_options' => false
            ]
        );

        $resolver->setNormalizer(
            'choices', 
            function (Options $options, $value) {
                /** @var EntityManager $em */
                $em = $options['em'];
                
                /** @var EntityRepository $repository */
                $repository = $em->getRepository('OroSegmentBundle:Segment');
                $entities   = $options['entities'];
                $qb = $this->getQueryBuilder($repository, $entities);
                return $qb->getQuery()->getResult();
        });
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(EntityRepository $er, $entities = null)
    {
        $qb = $er->createQueryBuilder('c');
        // Auf Segmente mit MemberEntity einschränken
        if (!empty($entities)) {
            $qb->andWhere($qb->expr()->in('c.entity', $entities));
            $qb->groupBy('c.name', 'c.id');
        }
        $qb->orderBy('c.name', 'ASC');

        return $qb;
    }
}
