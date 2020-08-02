<?php

namespace DMKClub\Bundle\MemberBundle\Provider;


use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use DMKClub\Bundle\MemberBundle\Entity\MemberFee;

class MemberFeeEntityNameProvider implements EntityNameProviderInterface
{
    public function getNameDQL($format, $locale, $className, $alias)
    {
        if (MemberFee::class !== $className) {
            return false;
        }

        return sprintf(<<<'EOF'
CONCAT('MemberFee ',%1$s.id, ' - price: ', %1$s.priceTotal, ' payed: ', %1$s.payedTotal)
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
        if (!$entity instanceof MemberFee) {
            return false;
        }

        if (EntityNameProviderInterface::FULL === $format) {
            return sprintf(
                '%d (price: %s / payed: %s)',
                $entity->getId(),
                number_format($entity->getPriceTotal() / 100, 2, ',', '.'),
                number_format($entity->getPayedTotal() / 100, 2, ',', '.'));
        }

        return sprintf(
            '%d (price: %s / payed: %s)',
            $entity->getId(),
            number_format($entity->getPriceTotal() / 100, 2, ',', '.'),
            number_format($entity->getPayedTotal() / 100, 2, ',', '.'));
    }
}
