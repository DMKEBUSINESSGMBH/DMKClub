<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use DMKClub\Bundle\MemberBundle\Accounting\SimpleProcessor;
use PHPUnit\Framework\TestCase;

class SimpleProcessorTest extends TestCase
{

    public function testGetLabel()
    {
        $emMock = $this->getEMMockBuilder()->getMock();
        $processor = new SimpleProcessor($emMock);
        $this->assertEquals('dmkclub.member.accounting.processor.simple', $processor->getLabel(), 'Label is wrong');
    }

    protected function getEMMockBuilder()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor();
    }
}
