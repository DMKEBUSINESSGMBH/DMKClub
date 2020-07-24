<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\AddressBundle\Form\Type\AddressType;
use Oro\Bundle\ChannelBundle\Form\Type\ChannelSelectType;
use Oro\Bundle\ContactBundle\Form\Type\ContactSelectType;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;

use DMKClub\Bundle\PaymentBundle\Form\Type\BankAccountType;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;

class MemberType extends AbstractType
{

    const LABEL_PREFIX = 'dmkclub.member.';

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
        $builder->add('memberCode', TextType::class, [
                'required' => true,
                'label' => 'dmkclub.member.member_code.label'
            ])
            ->add('startDate', OroDateType::class, [
                'required' => false,
                'label' => 'dmkclub.member.start_date.label'
            ])
            ->add('endDate', OroDateType::class, array(
                'required' => false,
                'label' => 'dmkclub.member.end_date.label'
            ))
            ->add('name', TextType::class, array(
                'required' => true,
                'label' => 'dmkclub.member.name.label'
            ))
            ->add('status', MemberStatusType::class, array(
                'required' => true,
                'label' => 'dmkclub.member.status.label'
            ))
            ->add('paymentOption', EnumSelectType::class, [
                'required' => true,
                'label' => self::LABEL_PREFIX . 'payment_option.label',
                'enum_code' => PaymentOption::INTERNAL_ENUM_CODE
            ])
            ->add('paymentInterval', EnumSelectType::class, [
                'required' => true,
                'enum_code' => PaymentInterval::INTERNAL_ENUM_CODE,
                'label' => self::LABEL_PREFIX . 'payment_interval.label'
            ])
            ->add('isActive', CheckboxType::class, array(
                'tooltip' => $this->translator->trans('dmkclub.member.isActive.help'),
                'label' => 'dmkclub.member.is_active.label',
                'required' => false
            ))
            ->add('isHonorary', CheckboxType::class, array(
                'required' => false,
                'label' => 'dmkclub.member.is_honorary.label'
            ))
            ->add('isFreeOfCharge', CheckboxType::class, array(
                'required' => false,
                'label' => 'dmkclub.member.is_free_of_charge.label'
            ));
        // ->add('owner')
        // ->add('organization')

    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
        // tags disabled in 1.9
        // $builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));
        $builder->add('bankAccount', BankAccountType::class, [
            'label' => 'dmkclub.member.bank_account.label',
            'required' => false
        ]);
        $builder->add('contact', ContactSelectType::class, [
            'label' => 'dmkclub.member.contact.label',
            'required' => true
        ]);
        $builder->add('legalContact', ContactSelectType::class, [
            'label' => 'dmkclub.member.legal_contact.label',
            'required' => false
        ]);
        $builder->add('postalAddress', AddressType::class, [
            'required' => false
        ]);
        $builder->add('dataChannel', ChannelSelectType::class, [
            'required' => true,
            'label' => 'oro.sales.b2bcustomer.data_channel.label',
            'entities' => [
                'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
            ]
        ]);
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\Member',
            'cascade_validation' => true
        ]);
    }
}
