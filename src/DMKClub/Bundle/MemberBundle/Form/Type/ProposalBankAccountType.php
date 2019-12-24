<?php
namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use DMKClub\Bundle\PaymentBundle\Form\Type\BankAccountType;

class ProposalBankAccountType extends AbstractType
{
     /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'DMKClub\Bundle\MemberBundle\Entity\MemberProposalBankAccount',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return BankAccountType::class;
    }
}
