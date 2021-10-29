<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use DMKClub\Bundle\MemberBundle\Accounting\SimpleProcessor;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class SimpleProcessorTest extends TestCase
{

    private $logger;

    public function setUp(): void
    {
        $this->logger = new NullLogger();
    }

    public function testGetLabel()
    {
        $processor = new SimpleProcessor($this->logger);
        $this->assertEquals('dmkclub.member.accounting.processor.simple', $processor->getLabel(), 'Label is wrong');
    }
}
