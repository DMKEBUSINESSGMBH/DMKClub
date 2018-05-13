<?php

namespace DMKClub\Bundle\MemberBundle\Provider;


use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use DMKClub\Bundle\MemberBundle\Entity\Member;

class MemberEntityNameProvider implements EntityNameProviderInterface
{
    public function getNameDQL($format, $locale, $className, $alias)
    {
        if (Member::class !== $className) {
            return false;
        }

        return sprintf(<<<'EOF'
CONCAT(%1$s.name, CONCAT(', ', %1$s.memberCode))
EOF
            , $alias);
    }

    /**
     *
     * {@inheritDoc}
     * @see \Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface::getName()
     */
    public function getName($format, $locale, $entity)
    {
        if (!$entity instanceof Member) {
            return false;
        }

        if (EntityNameProviderInterface::FULL === $format) {
            return sprintf(
                '%s (%s / %s)',
                $entity->getName(),
                $entity->getMemberCode(),
                $entity->getStatus());
        }

        return sprintf(
            '%s (%s / %s)',
            $entity->getName(),
            $entity->getMemberCode(),
            $entity->getStatus());
    }
}
