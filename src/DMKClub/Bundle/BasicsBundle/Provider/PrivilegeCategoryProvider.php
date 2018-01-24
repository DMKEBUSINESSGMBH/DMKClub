<?php
namespace DMKClub\Bundle\BasicsBundle\Provider;

use Oro\Bundle\UserBundle\Model\PrivilegeCategory;
use Oro\Bundle\UserBundle\Provider\PrivilegeCategoryProviderInterface;

class PrivilegeCategoryProvider implements PrivilegeCategoryProviderInterface
{

    const NAME = 'dmkclub_data';

    /**
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getRolePrivilegeCategory()
    {
        return new PrivilegeCategory(self::NAME, 'dmkclub.basics.privilege.category.dmkclub_data.label', true, 9);
    }
}
