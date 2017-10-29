<?php

namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Entity;

use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;

class MemberFeeDiscountTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider dataProvider4Contains
	 */
	public function testContains($start, $end, $testDay, $expected){

		$discount = new MemberFeeDiscount();
		$discount->setStartDate(new \DateTime($start));
		$discount->setEndDate($end ? new \DateTime($end) : NULL);
		$current = $discount->contains(new \DateTime($testDay));
		$this->assertEquals($expected, $current);
	}

	public function dataProvider4Contains() {
		return  [
				['2016-10-01', '2016-10-11', '2016-10-01', true],
				['2016-10-01', '2016-10-11', '2016-10-11', true],
				['2016-10-01', '2016-10-11', '2016-10-12', false],
				['2016-10-01', '2016-10-11', '2016-10-02', true],
				['2016-10-01', NULL, '2016-10-02', true],
				['2016-10-01', NULL, '2015-10-02', false],
				['2016-10-01', '2016-10-11', '2016-10-20', false],
		];
	}

}
