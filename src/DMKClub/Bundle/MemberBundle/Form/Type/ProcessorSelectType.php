<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use DMKClub\Bundle\MemberBundle\Accounting\ProcessorProvider;

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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => $this->processorProvider->getVisibleProcessorChoices()
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dmkclub_member_accounting_processor_select';
    }
}
