<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

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
        return 'genemu_jqueryselect2_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $queryBuilderNormalizer = function (Options $options, $qb) {
            /** @var EntityManager $em */
            $em = $options['em'];

            /** @var EntityRepository $repository */
            $repository = $em->getRepository('OroSegmentBundle:Segment');
            $entities   = $options->get('entities');

            return $qb($repository, $entities);
        };

        $resolver->setDefaults(
            [
                'label'         => 'oro.segment.entity_label',
                'class'         => 'OroSegmentBundle:Segment',
                'property'      => 'name',
                'random_id'     => true,
                'query_builder' => $this->getQueryBuilder(),
                'configs'       => [
                    'allowClear'  => true,
                    'placeholder' => 'oro.segment.condition_builder.choose_entity_segment'
                ],
                'entities'      => [],
                'translatable_options' => false
            ]
        );

        $resolver->setNormalizers(['query_builder' => $queryBuilderNormalizer]);
    }

    /**
     * @return callable
     */
    private function getQueryBuilder()
    {
        return function (EntityRepository $er, $entities = null) {
            $query = $er->createQueryBuilder('c');
            // Auf Segmente mit MemberEntity einschränken
            if (!empty($entities)) {
                $query->andWhere($query->expr()->in('c.entity', $entities));
                $query->groupBy('c.name', 'c.id');
            }
            $query->orderBy('c.name', 'ASC');

            return $query;
        };
    }
}
