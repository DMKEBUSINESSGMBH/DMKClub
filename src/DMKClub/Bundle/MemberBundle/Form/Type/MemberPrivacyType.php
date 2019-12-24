<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MemberPrivacyType extends AbstractType {

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
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->buildPlainFields($builder, $options);
		$this->buildRelationFields($builder, $options);
	}
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	protected function buildPlainFields(FormBuilderInterface $builder, array $options) {
		$builder
			->add('signDate', OroDateType::class, ['required' => true, 'label' => 'dmkclub.member.memberprivacy.sign_date.label'])
			->add('emailAllowed', CheckboxType::class, [
			    'tooltip' => $this->translator->trans('dmkclub.member.memberprivacy.email_allowed.help'),
			    'label' => 'dmkclub.member.memberprivacy.email_allowed.label',
			    'required' => false])
			->add('phoneAllowed', CheckboxType::class, [
    	        'tooltip' => $this->translator->trans('dmkclub.member.memberprivacy.phone_allowed.help'),
    	        'label' => 'dmkclub.member.memberprivacy.phone_allowed.label',
    	        'required' => false])
 	        ->add('postalAllowed', CheckboxType::class, [
	            'tooltip' => $this->translator->trans('dmkclub.member.memberprivacy.postal_allowed.help'),
	            'label' => 'dmkclub.member.memberprivacy.postal_allowed.label',
	            'required' => false])
            ->add('sharingAllowed', CheckboxType::class, [
                'tooltip' => $this->translator->trans('dmkclub.member.memberprivacy.sharing_allowed.help'),
                'label' => 'dmkclub.member.memberprivacy.sharing_allowed.label',
                'required' => false])
            ->add('merchandisingAllowed', CheckboxType::class, [
                'tooltip' => $this->translator->trans('dmkclub.member.memberprivacy.merchandising_allowed.help'),
                'label' => 'dmkclub.member.memberprivacy.merchandising_allowed.label',
                'required' => false])
		;

	}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options){
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberPrivacy',
                'cascade_validation' => true,
            ]
        );
    }
}
