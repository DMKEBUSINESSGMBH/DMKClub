<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
        ->add('paymentOption', 'dmkclub_paymentoptions', [
            'required' => true,
            'label' => 'dmkclub.member.payment_option.label'
        ])
        ->add('paymentInterval', 'dmkclub_paymentintervals', [
            'required' => true,
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
        $builder->add('dataChannel', 'orocrm_channel_select_type', [
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
