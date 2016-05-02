<?php

namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Accounting\Time\TimeSpanCalculator;

class TimeSpanCalculatorTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider timeSpanProvider
	 */
	public function testCalculateSpanInMonth($start, $end, $expected){

		$calculator = new TimeSpanCalculator();

		$span = $calculator->calculateTimePeriods($start, $end, 0);
		$this->assertEquals($expected, count($span));

	}

	public function timeSpanProvider() {
		return [
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), 12],
				[new \DateTime('2016-07-01'), new \DateTime('2016-07-02'), 1],
				[new \DateTime('2016-07-01'), new \DateTime('2016-08-01'), 2],
				[new \DateTime('2016-08-01'), new \DateTime('2016-07-01'), 2],
		];
	}
}
