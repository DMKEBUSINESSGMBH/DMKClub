<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Repository;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository {
	/**
	 * Get members active and inactive
	 *
	 * @return array
	 */
	public function getMembersInActive() {
		// SELECT is_active, count(id) FROM `dmkclub_member`
		// WHERE end_date IS NULL
		// GROUP BY is_active
		// select statuses
    $qb = $this->createQueryBuilder('m');
		$qb->select('m.isActive, count(m.id) cnt')
			->where('m.endDate IS NULL')
			->groupBy('m.isActive');

		$resultData = array();
		$data = $qb->getQuery()->getArrayResult();
		foreach ($data as $row) {
			$resultData[$row['isActive'] ? 'active' : 'passive'] = (int)$row['cnt'];
		}

		return $resultData;
	}

	/**
	 * Members grouped by gender
	 * @return multitype:number
	 */
	public function getMembersGender() {

		// select statuses
		$qb = $this->createQueryBuilder('m');
		$qb->select('c.gender, count(m.id) cnt')
		->join('m.contact', 'c')
		->where('m.endDate IS NULL')
		->groupBy('c.gender');

		$resultData = array();
		$data = $qb->getQuery()->getArrayResult();
		foreach ($data as $row) {
			$resultData[$row['gender'] ? $row['gender'] : 'unknown'] = (int)$row['cnt'];
		}

		return $resultData;
	}

	public function getNewMembersByYear() {
// 		SELECT YEAR(start_date), count(m.id) FROM `dmkclub_member` m
// 		WHERE 1
// 		GROUP BY YEAR(start_date)
// 		ORDER BY YEAR(start_date) desc
// 		LIMIT 10
		$qb = $this->createQueryBuilder('m');
		$qb->select('YEAR(m.startDate), count(m.id) cnt')
//		->where('m.endDate IS NULL')
			->groupBy('YEAR(m.startDate)')
			->orderBy('YEAR(m.startDate)', 'desc')
			->setMaxResults(10);

	}
}
