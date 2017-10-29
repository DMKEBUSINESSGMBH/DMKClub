<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use DMKClub\Bundle\PaymentBundle\Model\PaymentOption;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;

class MemberProposalType extends AbstractType
{
    const LABEL_PREFIX = 'dmkclub.member.memberproposal.';
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param ConfigManager       $configManager
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
        $builder->add('submit', 'submit');
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('namePrefix', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'name_prefix.label'
        ])
        ->add('firstName', 'text', [
            'required' => true,
            'label' => self::LABEL_PREFIX.'first_name.label'
        ])
        ->add('middleName', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'middle_name.label'
        ])
        ->add('lastName', 'text', [
            'required' => true,
            'label' => self::LABEL_PREFIX.'last_name.label'
        ])
        ->add('nameSuffix', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'name_suffix.label'
        ])
        ->add(
            'status',
            'oro_enum_select',
            [
                'label' => self::LABEL_PREFIX.'status.label',
                'enum_code' => 'memberproposal_status',
                'required' => true,
                'constraints' => [new Assert\NotNull()]
            ]
            )
        ->add('birthday', 'oro_date', [
            'required' => false,
            'years' => ['1800', date('Y')],
            'label' => self::LABEL_PREFIX.'birthday.label'
        ])
        ->add('paymentOption', 'oro_enum_select', [
            'required' => true,
            'enum_code' => PaymentOption::INTERNAL_ENUM_CODE,
            'label' => 'dmkclub.member.payment_option.label'
        ])
        ->add('paymentInterval', 'oro_enum_select', [
            'required' => true,
            'enum_code' => PaymentInterval::INTERNAL_ENUM_CODE,
            'label' => self::LABEL_PREFIX.'payment_interval.label'
        ])
        ->add('emailAddress', 'text', [
            'required' => true,
            'label' => self::LABEL_PREFIX.'email_address.label'
        ])
        ->add('phone', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'phone.label'
        ])
        ->add('isActive', 'checkbox', [
            'tooltip' => $this->translator->trans('dmkclub.member.isActive.help'),
            'label' => 'dmkclub.member.is_active.label',
            'required' => false
        ])
        ->add('jobTitle', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'job_title.label'
        ])
        ->add('discountReason', 'text', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_reason.label'
        ])
        ->add('discountStartDate', 'oro_date', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_start_date.label'

        ])
        ->add('discountEndDate', 'oro_date', [
            'required' => false,
            'label' => self::LABEL_PREFIX.'discount_end_date.label'

        ])
        ->add('comment', 'textarea', [
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
        $builder->add('postalAddress', 'dmkclub_member_proposal_address', [
            'cascade_validation' => true,
            'required' => false
        ]);
        $builder->add('bankAccount', 'dmkclub_member_proposal_bankaccount', [
            'label' => 'dmkclub.member.bank_account.label',
            'required' => false
        ]);
        $builder->add('dataChannel', 'oro_channel_select_type', [
            'required' => true,
            'label' => 'orocrm.sales.b2bcustomer.data_channel.label',
            'entities' => [
                'DMKClub\\Bundle\\MemberBundle\\Entity\\Member'
            ]
        ]);
    }

    /**
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberProposal',
            'dataChannelField' => false
        ));
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getBlockPrefix()
    {
        return 'dmkclub_member_memberproposal';
    }
}
