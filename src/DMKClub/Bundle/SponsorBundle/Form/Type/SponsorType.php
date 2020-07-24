<?php
namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\ContactBundle\Form\Type\ContactSelectType;
use Oro\Bundle\AccountBundle\Form\Type\AccountSelectType;
use Oro\Bundle\ChannelBundle\Form\Type\ChannelSelectType;
use Oro\Bundle\AddressBundle\Form\Type\AddressType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsorType extends AbstractType
{
    const LABEL_PREFIX = 'dmkclub.sponsor.';

    /** @var TranslatorInterface */
    protected $translator;

    /**
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'required' => true,
                'label' => self::LABEL_PREFIX . 'name.label'
            ])
            ->add('isActive', CheckboxType::class, [
                'tooltip' => $this->translator->trans('dmkclub.sponsor.is_active.help'),
                'label' => 'dmkclub.sponsor.is_active.label',
                'required' => false
            ]);
        $builder->add('contact', ContactSelectType::class, [
            'label' => self::LABEL_PREFIX . 'contact.label',
            'required' => true
        ]);
        $builder->add('account', AccountSelectType::class, [
            'label' => self::LABEL_PREFIX . 'account.label',
            'required' => false
        ]);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
        // tags removed in 1.9
        // $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));
        $builder->add('dataChannel', ChannelSelectType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX . 'data_channel.label',
            'entities' => [
                'DMKClub\\Bundle\\SponsorBundle\\Entity\\Sponsor'
            ]
        ]);

        // sponsor categories
        $builder->add('category', CategorySelectType::class, [
            'label' => self::LABEL_PREFIX . 'category.entity_label',
            'required' => false
        ]);
        $builder->add('billingAddress', AddressType::class, [
            'required' => false
        ]);
        $builder->add('postalAddress', AddressType::class, [
            'required' => false
        ]);
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\SponsorBundle\Entity\Sponsor',
            'cascade_validation' => true
        ]);
    }
}
