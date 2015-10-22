<?php
namespace DMKClub\Bundle\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;


class BankAccountType extends AbstractType {
	const NAME = 'dmkclub_bankaccount';

	public function __construct() {
	}
	/**
	 * @return string
	 */
	public function getName()
	{
		return self::NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('id', 'hidden')
			->add('accountOwner', 'text', array('required' => false, 'label' => 'dmkclub.bank_account.account_owner.label'))
			->add('iban', 'text', array('required' => false, 'label' => 'dmkclub.bank_account.iban.label'))
			->add('bic', 'text', array('required' => false, 'label' => 'dmkclub.bank_account.bic.label'))
			->add('bankName', 'text', array('required' => false, 'label' => 'dmkclub.bank_account.bank_name.label'))
			;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'           => 'DMKClub\Bundle\PaymentBundle\Entity\BankAccount',
				'intention'            => 'bankaccount',
				'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
				'single_form'          => true
			)
		);
	}

}

