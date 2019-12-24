<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Oro\Bundle\ChannelBundle\Form\Type\ChannelSelectType;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;

use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MemberProposalType extends AbstractType
{
    const LABEL_PREFIX = 'dmkclub.member.memberproposal.';
    /** @var TranslatorInterface */
    protected $translator;

    /**
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
//        $builder->add('submit', SubmitType::class);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('namePrefix', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'name_prefix.label'
        ])
        ->add('firstName', TextType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'first_name.label'
        ])
        ->add('middleName', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'middle_name.label'
        ])
        ->add('lastName', TextType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'last_name.label'
        ])
        ->add('nameSuffix', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'name_suffix.label'
        ])
        ->add(
            'status',
            EnumSelectType::class,
            [
                'label' => self::LABEL_PREFIX.'status.label',
                'enum_code' => 'memberproposal_status',
                'required' => true,
                'constraints' => [new Assert\NotNull()]
            ]
            )
        ->add('birthday', OroDateType::class, [
            'required' => false,
            'years' => ['1800', date('Y')],
            'label' => self::LABEL_PREFIX.'birthday.label'
        ])
        ->add('paymentOption', EnumSelectType::class, [
            'required' => true,
            'enum_code' => PaymentOption::INTERNAL_ENUM_CODE,
            'label' => 'dmkclub.member.payment_option.label'
        ])
        ->add('paymentInterval', EnumSelectType::class, [
            'required' => true,
            'enum_code' => PaymentInterval::INTERNAL_ENUM_CODE,
            'label' => self::LABEL_PREFIX.'payment_interval.label'
        ])
        ->add('emailAddress', TextType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'email_address.label'
        ])
        ->add('phone', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'phone.label'
        ])
        ->add('isActive', CheckboxType::class, [
            'tooltip' => $this->translator->trans('dmkclub.member.isActive.help'),
            'label' => 'dmkclub.member.is_active.label',
            'required' => false
        ])
        ->add('jobTitle', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'job_title.label'
        ])
        ->add('discountReason', TextType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_reason.label'
        ])
        ->add('discountStartDate', OroDateType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_start_date.label'

        ])
        ->add('discountEndDate', OroDateType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_end_date.label'

        ])
        ->add('comment', TextareaType::class, [
            'required' => false,
            'label' => self::LABEL_PREFIX.'comment.label'
        ]);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *            @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('postalAddress',
            ProposalAddressType::class, [
                'required' => false
        ]);
        $builder->add('bankAccount', 
            ProposalBankAccountType::class, [
                'label' => 'dmkclub.member.bank_account.label',
                'required' => false
        ]);
        $builder->add('dataChannel', 
            ChannelSelectType::class, [
                'required' => true,
                'label' => 'orocrm.sales.b2bcustomer.data_channel.label',
                'entities' => [
                    'DMKClub\\Bundle\\MemberBundle\\Entity\\MemberProposal'
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
            'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberProposal',
            'dataChannelField' => false
        ]);
    }
}
