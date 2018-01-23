<?php
namespace DMKClub\Bundle\MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use DMKClub\Bundle\MemberBundle\Model\ExtendMemberProposalBankAccount;

/**
 * @ORM\Table(name="dmkclub_member_proposal_bank")
 * @ORM\Entity()
 * @Config(
 *   defaultValues={
 *     "entity"={
 *       "icon"="fa-briefcase"
 *     },
 *     "note"={
 *       "immutable"=true
 *     },
 *     "activity"={
 *       "immutable"=true
 *     },
 *     "attachment"={
 *       "immutable"=true
 *     }
 *   }
 * )
 */
class MemberProposalBankAccount extends ExtendMemberProposalBankAccount
{
}
