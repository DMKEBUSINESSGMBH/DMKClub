<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessorSelectType extends AbstractType
{
    /**
     * @var ProcessorProvider
     */
    protected $processorProvider;

    /**
     * @param ProcessorProvider $processorProvider
     */
    public function __construct(ProcessorProvider $processorProvider)
    {
        $this->processorProvider = $processorProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => array_flip($this->processorProvider->getVisibleProcessorChoices())
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
