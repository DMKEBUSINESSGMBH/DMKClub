<?php

namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use OroCRM\Bundle\ContactBundle\Entity\Contact;
use DMKClub\Bundle\MemberBundle\Accounting\SimpleProcessor;

class SimpleProcessorTest extends \PHPUnit_Framework_TestCase {
	public function testGetLabel(){
		$emMock = $this->getEMMockBuilder()->getMock();
		$processor = new SimpleProcessor($emMock);
		$this->assertEquals('dmkclub.member.accounting.processor.simple',$processor->getLabel(), 'Label is wrong');
	}

	protected function getEMMockBuilder() {
		return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
			->disableOriginalConstructor();
	}
}
