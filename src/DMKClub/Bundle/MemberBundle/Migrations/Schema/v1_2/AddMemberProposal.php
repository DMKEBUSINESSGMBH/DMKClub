<?php

namespace DMKClub\Bundle\MemberBundle\Migrations\Schema\v1_2;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use DMKClub\Bundle\MemberBundle\Migrations\Schema\DMKClubMemberBundleInstaller;

class AddMemberProposal implements Migration, ActivityExtensionAwareInterface, CommentExtensionAwareInterface{

    /** @var CommentExtension */
    protected $comment;

    /** @var ActivityExtension */
    protected $activityExtension;

    /**
     * @param CommentExtension $commentExtension
     */
    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }
    /**
     * {@inheritdoc}
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

	/**
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries) {
	    DMKClubMemberBundleInstaller::createDmkclubMemberProposalBankaccountTable($schema);
 	    DMKClubMemberBundleInstaller::createDmkclubMemberProposalAddressTable($schema);
 	    DMKClubMemberBundleInstaller::createDmkclubMemberProposalTable($schema);

	    DMKClubMemberBundleInstaller::addDmkclubMemberProposalAddressForeignKeys($schema);
	    DMKClubMemberBundleInstaller::addDmkclubMemberProposalForeignKeys($schema);

	    $this->comment->addCommentAssociation($schema, 'dmkclub_member_proposal');
	    DMKClubMemberBundleInstaller::addActivityAssociations4Proposal($schema, $this->activityExtension);

	}
}