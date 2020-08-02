<?php
namespace DMKClub\Bundle\MemberBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;

class MemberFeeRepository extends EntityRepository
{

    /**
     *
     * @param array $ids
     * @param MemberBilling $billingId
     * @param string|string[]|null $folderType
     * @param bool $isAllSelected
     *
     * @return QueryBuilder
     */
    public function getMemberFeeBuilderForMassAction($ids, $billing, $isAllSelected)
    {
        $queryBuilder = $this->createQueryBuilder('f');

        if ($billing) {
            $this->applyBillingFilter($queryBuilder, $billing);
        }

        if (! $isAllSelected) {
            $this->applyIdFilter($queryBuilder, $ids);
        }
        return $queryBuilder;
    }

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param MemberBilling $billing
     *
     * @return $this
     */
    protected function applyBillingFilter(QueryBuilder $queryBuilder, MemberBilling $billing)
    {
        $queryBuilder->andWhere('f.billing = ?1');
        $queryBuilder->setParameter(1, $billing);

        return $this;
    }

    /**
     *
     * @param QueryBuilder $queryBuilder
     * @param array $ids
     *
     * @return $this
     */
    protected function applyIdFilter(QueryBuilder $queryBuilder, $ids)
    {
        if ($ids) {
            $queryBuilder->andWhere($queryBuilder->expr()
                ->in('f.id', $ids));
        }

        return $this;
    }
}
