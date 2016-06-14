<?php

namespace DMKClub\Bundle\MemberBundle\Entity\Repository;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class MemberRepository extends EntityRepository {
	const MEMBER_TYPE_ALL = 'all';
	const MEMBER_TYPE_ACTIVE = 'active';
	const MEMBER_TYPE_PASSIVE = 'passive';

	/**
	 * Get members active and inactive
	 *
	 * @return array
	 */
	public function getMembersActivePassive() {
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
	public function getMembersGender($memberType) {

		// select statuses
		$qb = $this->createQueryBuilder('m');
		$qb->select('c.gender, count(m.id) cnt')
		->join('m.contact', 'c')
		->where('m.endDate IS NULL')
		->groupBy('c.gender');
		if($memberType == self::MEMBER_TYPE_ACTIVE || $memberType == self::MEMBER_TYPE_PASSIVE) {
			$qb->andWhere('m.isActive = :mtype');
			$qb->setParameter('mtype', ($memberType == self::MEMBER_TYPE_ACTIVE ? '1' : '0'));
		}

		$resultData = array();
		$data = $qb->getQuery()->getArrayResult();
		foreach ($data as $row) {
			$resultData[$row['gender'] ? $row['gender'] : 'unknown'] = (int)$row['cnt'];
		}

		return $resultData;
	}

	protected function getMemberTypeClause($memberType, $alias) {
		switch ($memberType) {
			case self::MEMBER_TYPE_ACTIVE:
				return ' AND '.$alias.'is_active=1';
			case self::MEMBER_TYPE_PASSIVE:
				return ' AND '.$alias.'is_active=0';
			default:
				return '';
		}
	}
	public function getMemberByAge($memberType) {
// 		SELECT count(m.id), FLOOR(TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())/10) AS age
// 		FROM `dmkclub_member` m
// 		JOIN orocrm_contact c ON c.id = m.contact_id
// 		WHERE  TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())  > 0 AND TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())  <= 110
// 		GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())/10)
// 		ORDER BY age desc

		// build rsm here
		$sql = 'SELECT count(m.id) cnt, FLOOR(TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())/10) AS age
		FROM `dmkclub_member` m
		JOIN orocrm_contact c ON c.id = m.contact_id
		WHERE  TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())  > 0 AND TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())  <= 110 ';
		$sql .= $this->getMemberTypeClause($memberType, 'm.');
		$sql .= ' GROUP BY FLOOR(TIMESTAMPDIFF(YEAR, c.birthday, CURDATE())/10)
		ORDER BY age desc
				';

		$stmt = $this->_em->getConnection()->prepare($sql);
		$stmt->execute();
		$data = $stmt->fetchAll();

		$resultData = array();
		foreach ($data as $row) {
			$age = $row['age'];
			$label = $age .'0-'.($age+1) . '0: ' . $row['cnt'];
      $resultData[]    = ['label' => $label, 'value' => (int)$row['cnt'],];
		}

		return $resultData;
	}

	public function getNewMembersByYear($memberType) {

		// build rsm here
		$sql = 'SELECT YEAR(start_date) year, count(id) cnt
				FROM dmkclub_member WHERE 1=1 ';
		$sql .= $this->getMemberTypeClause($memberType, '');
		$sql .=	' GROUP BY YEAR(start_date)
				ORDER BY YEAR(start_date) desc
				LIMIT 20
				';

		$stmt = $this->_em->getConnection()->prepare($sql);
		$stmt->execute();
		$data = $stmt->fetchAll();
		$data = array_reverse($data);

 		$resultData = array();
		foreach ($data as $row) {
			$resultData[] = [
				'label' => $row['year'],
				'value' => (int)$row['cnt'],
			];
		}
		return $resultData;
	}
}
