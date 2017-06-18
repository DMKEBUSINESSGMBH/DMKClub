<?php

namespace DMKClub\Bundle\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;

use DMKClub\Bundle\PaymentBundle\Model\ExtendBankAccount;

/**
 * Class BankAccount
 *
 * @ORM\Table(name="dmkclub_bankaccount")
 * @ORM\Entity(repositoryClass="DMKClub\Bundle\PaymentBundle\Entity\Repository\BankAccountRepository")
 * @Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-briefcase"
 *          },
 *          "note"={
 *              "immutable"=true
 *          },
 *          "activity"={
 *              "immutable"=true
 *          },
 *          "attachment"={
 *              "immutable"=true
 *          }
 *      }
 * )
 */
class BankAccount extends ExtendBankAccount {

}
