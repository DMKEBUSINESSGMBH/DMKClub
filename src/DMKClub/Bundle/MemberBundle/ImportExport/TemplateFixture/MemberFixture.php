<?php
namespace DMKClub\Bundle\MemberBundle\ImportExport\TemplateFixture;

use Oro\Bundle\ImportExportBundle\TemplateFixture\AbstractTemplateRepository;
use Oro\Bundle\ImportExportBundle\TemplateFixture\TemplateFixtureInterface;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Model\MemberStatus;
use DMKClub\Bundle\PaymentBundle\Entity\BankAccount;

class MemberFixture extends AbstractTemplateRepository implements TemplateFixtureInterface
{

    const DUMMY_MEMBER_NAME = 'Jerry Coleman';

    /**
     *
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return \DMKClub\Bundle\MemberBundle\Entity\Member::class;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->getEntityData(self::DUMMY_MEMBER_NAME);
    }

    /**
     *
     * {@inheritdoc}
     */
    protected function createEntity($key)
    {
        return new Member();
    }

    /**
     *
     * @param string $key
     * @param Member $entity
     */
    public function fillEntityData($key, $entity)
    {
        $addressRepo = $this->templateManager->getEntityRepository('Oro\Bundle\AddressBundle\Entity\Address');
        $userRepo = $this->templateManager->getEntityRepository('Oro\Bundle\UserBundle\Entity\User');
        $contactRepo = $this->templateManager->getEntityRepository('Oro\Bundle\ContactBundle\Entity\Contact');
        $channelRepo = $this->templateManager->getEntityRepository('Oro\Bundle\ChannelBundle\Entity\Channel');
        $organizationRepo = $this->templateManager->getEntityRepository('Oro\Bundle\OrganizationBundle\Entity\Organization');

        switch ($key) {
            case self::DUMMY_MEMBER_NAME:
                // $entity->setName ( 'DMK Club. Member Name' );
                $entity->setOwner($userRepo->getEntity('John Doo'));
                $entity->setOrganization($organizationRepo->getEntity('default'));
                $entity->setDataChannel($channelRepo->getEntity('Member channel|member'));
                $entity->setCreatedAt(new \DateTime());
                $entity->setUpdatedAt(new \DateTime());
                $contact = $contactRepo->getEntity(self::DUMMY_MEMBER_NAME);
                $contact->setBirthday(new \DateTime());
                $entity->setContact($contact);
                $entity->setPostalAddress($addressRepo->getEntity(self::DUMMY_MEMBER_NAME));
                // $entity->setEmail ( 'mb@chemnitzerfc.de' );
                $entity->setName(self::DUMMY_MEMBER_NAME);
                $bankAccount = new BankAccount();
                $bankAccount->setBankName('HonestBank')
                    ->setIban('DE12345678')
                    ->setBic('DEDE123BANK')
                    ->setDirectDebitValidFrom(new \DateTime());
                $entity->setBankAccount($bankAccount);
                $entity->setIsActive(TRUE);
                $entity->setStatus(MemberStatus::ACTIVE);
                $entity->setStartDate(new \DateTime());
                // TODO: ENUM export prüfen
                // $entity->setPaymentOption(PaymentOption::SEPA_DIRECT_DEBIT);
                $entity->setMemberCode(123);

                return;
        }

        parent::fillEntityData($key, $entity);
    }
}
