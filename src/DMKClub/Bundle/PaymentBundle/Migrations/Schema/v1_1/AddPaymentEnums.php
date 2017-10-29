<?php

namespace DMKClub\Bundle\PaymentBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;
use DMKClub\Bundle\PaymentBundle\Migrations\Schema\DMKClubPaymentBundleInstaller;

class AddPaymentEnums implements Migration,
    ExtendExtensionAwareInterface,
    OrderedMigrationInterface
{

    /** @var ExtendExtension */
    protected $extendExtension;

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
    /**
     * @param ExtendExtension $extendExtension
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

	/**
	 *
	 * @inheritdoc
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function up(Schema $schema, QueryBag $queries)
	{
	    DMKClubPaymentBundleInstaller::addPaymentIntervalEnum($schema, $queries, $this->extendExtension);
	    DMKClubPaymentBundleInstaller::addPaymentOptionEnum($schema, $queries, $this->extendExtension);
	}

}
