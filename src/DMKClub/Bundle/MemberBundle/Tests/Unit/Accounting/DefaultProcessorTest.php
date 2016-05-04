<?php

namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use OroCRM\Bundle\ContactBundle\Entity\Contact;
use Psr\Log\NullLogger;

class DefaultProcessorTest extends \PHPUnit_Framework_TestCase {
	private $logger;
	public function setUp() {
		$this->logger = new NullLogger();
	}

	public function testGetLabel(){
		$emMock = $this->getEMMockBuilder()->getMock();
		$processor = new DefaultProcessor($this->logger, $emMock);
		$this->assertEquals('dmkclub.member.accounting.processor.default',$processor->getLabel(), 'Label is wrong');
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testExecute($start, $end, $options, $member, $expectedFee, $tag){
		$emMock = $this->getEMMockBuilder()->getMock();
		$processor = new DefaultProcessor($this->logger, $emMock);
		$memberBilling = new MemberBilling();
		$memberBilling->setStartDate($start);
		$memberBilling->setEndDate($end);

		$processor->init($memberBilling, $options);
		$memberFee = $processor->execute($member);

		$this->assertInstanceOf('\DMKClub\Bundle\MemberBundle\Entity\MemberFee', $memberFee, 'Result is not instance of memberfee');

		$positions = $memberFee->getPositions();
		$this->assertInstanceOf('\IteratorAggregate', $positions, 'Result is not instance of IteratorAggregate');
		$this->assertEquals(1, count($positions), 'Default processor should return exactly one position');
		/* @var $position \DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition */
		$position = $positions[0];
		$this->assertInstanceOf('\DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition', $position, 'Position is not instance of MemberFeePosition');

		$this->assertEquals($expectedFee, $position->getPriceTotal(), $tag.' - Price total is wrong');
		$this->assertEquals($expectedFee, $position->getPriceSingle(), $tag.' - Price single is wrong');

		$this->assertEquals($expectedFee, $memberFee->getPriceTotal(), $tag.' - Price in summary total is wrong');

	}

	public function dataProvider() {
		$year = (int)(new \DateTime(''))->format('Y');

		return [
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 520,
						'age_reduced' => 18,
				], $this->buildMember('2010-02-01', NULL, '1970-05-13'), 12000, 'simplefull'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 520,
						'age_reduced' => 18,
				], $this->buildMember('2016-08-01', NULL, '1970-05-13'), 11000, 'newfull'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 520,
						'age_reduced' => 18,
				], $this->buildMember('2015-08-01', '2016-08-01', '1970-05-13'), 2000, 'retiredfull'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 200,
						'age_reduced' => 18,
				], $this->buildMember('2010-02-01', NULL, ($year - 10).'-05-13'), 2400, 'simplereduced'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 200,
						'age_reduced' => 18,
				], $this->buildMember('2010-02-01', NULL, ($year - 17).'-05-13'), 4000, 'reduced2full'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 200,
						'age_reduced' => 18,
				], $this->buildMember('2010-02-01', '2017-05-01', ($year - 17).'-05-13'), 3000, 'reduced2fullretired'],
				[new \DateTime('2016-07-01'), new \DateTime('2017-06-30'), [
						'fee' => 1000,
						'fee_reduced' => 200,
						'age_reduced' => 18,
				], $this->buildMember('2016-08-01', NULL, ($year - 17).'-05-13'), 3800, 'reduced2fullnew'],
		];
	}

	protected function buildMember($start, $end, $birthday) {
		$contact = new Contact();
		$contact->setBirthday(new \DateTime($birthday));
		$member = new Member();
		$member->setContact($contact);
		$member->setStartDate(new \DateTime($start));
		$member->setEndDate($end ? new \DateTime($end) : NULL);
		return $member;
	}
	protected function getEMMockBuilder() {
		return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor();
	}
}
