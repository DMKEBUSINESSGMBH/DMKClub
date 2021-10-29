<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Accounting;

use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use Oro\Bundle\ContactBundle\Entity\Contact;
use Psr\Log\NullLogger;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use PHPUnit\Framework\TestCase;

class DefaultProcessorTest extends TestCase
{

    private $logger;

    public function setUp(): void
    {
        $this->logger = new NullLogger();
    }

    public function testGetLabel()
    {
        $emMock = $this->getEMMockBuilder()->getMock();
        $processor = new DefaultProcessor($this->logger, $emMock);
        $this->assertEquals('dmkclub.member.accounting.processor.default', $processor->getLabel(), 'Label is wrong');
    }

    /**
     *
     * @dataProvider dataProvider
     */
    public function testExecute($start, $end, $options, $member, $expectedFee, $expectedTotal, $positionCnt, $tag)
    {
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
        $this->assertEquals($positionCnt, count($positions), 'Default processor returned unexpected number of positions');
        /* @var $position \DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition */
        // Es muss immer eine Fee-Position vorhanden sein
        $positions = $memberFee->getPositionsByFlag(MemberFeePosition::FLAG_FEE);
        $position = $positions[0];
        $this->assertInstanceOf('\DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition', $position, 'Position is not instance of MemberFeePosition');

        $this->assertEquals($expectedFee, $position->getPriceTotal(), $tag . ' - Price total is wrong');
        $this->assertEquals($expectedFee, $position->getPriceSingle(), $tag . ' - Price single is wrong');

        $this->assertEquals($expectedTotal, $memberFee->getPriceTotal(), $tag . ' - Price in summary total is wrong');
    }

    public function dataProvider()
    {
        $year = 2016;
        $feeOptions = [
            DefaultProcessor::OPTION_FEE => 1000,
            DefaultProcessor::OPTION_FEE_DISCOUNT => 800,
            DefaultProcessor::OPTION_FEE_ADMISSION => 350,
            DefaultProcessor::OPTION_FEE_CHILD => 200,
            DefaultProcessor::OPTION_AGE_CHILD => 18
        ];

        return [
            // Vollzahler über die gesamte Laufzeit
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2010-02-01', NULL, '1970-05-13'),
                12000,
                12000,
                1,
                'simplefull'
            ],
            // Vollzahler mit Eintritt
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2016-08-01', NULL, '1970-05-13'),
                11000,
                11350,
                2,
                'newfull2'
            ],
            // Vollzahler mit Austritt
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2015-08-01', '2016-08-01', '1970-05-13'),
                2000,
                2000,
                1,
                'retiredfull'
            ],
            // Kinderbeitrag über gesamte Laufzeit
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2010-02-01', NULL, ($year - 10) . '-05-13'),
                2400,
                2400,
                1,
                'simplechild'
            ],
            // Kinderbeitrag die ersten 11 Monate. Ab 18 voller Preis (Geburtstag im Mai, voller Beitrag ab Juni)
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2010-02-01', NULL, ($year - 17) . '-05-13'),
                3200,
                3200,
                1,
                'child2full'
            ],
            // Kinderbeitrag die ersten 11 Monate. Austritt mit Volljährigkeit am 1.5. (Geburtstag im Mai)
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2010-02-01', '2017-05-01', ($year - 17) . '-05-13'),
                2200,
                2200,
                1,
                'child2fullretired'
            ],
            // Kinderbeitrag nach Neueintritt die ersten 10 Monate. Letzter Monat Vollzahler
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2016-08-01', NULL, ($year - 17) . '-05-13'),
                3000,
                3350,
                2,
                'child2fullnew'
            ],

            // Beitragsreduzierung über die gesamte Laufzeit
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2015-07-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2015-07-01', NULL)
                ]),
                9600,
                9600,
                1,
                'discountsimple'
            ],
            // Beitragsreduzierung mit Neuanmeldung
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2016-08-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2015-07-01', NULL)
                ]),
                8800,
                9150,
                2,
                'newdiscount'
            ],
            // Beitragsreduzierung endet nach 6 Monaten
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2015-08-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2015-07-01', '2016-12-31')
                ]),
                10800,
                10800,
                1,
                'discount2full'
            ],

            // Beitragsreduzierung für erste 6 Monate und letzte 2 Monate
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2015-08-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2015-07-01', '2016-12-31'),
                    $this->buildMemberFeeDiscount('2017-05-01', NULL)
                ]),
                10400,
                10400,
                1,
                'discount2full2discount'
            ],

            // Beitragsreduzierung für die letzten 2 Monate
            [
                new \DateTime('2016-07-01'),
                new \DateTime('2017-06-30'),
                $feeOptions,
                $this->buildMember('2015-08-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2017-05-01', NULL)
                ]),
                11600,
                11600,
                1,
                'full2discount'
            ],

            // Beitragsreduzierung für die letzten 2 Monate
            [
                new \DateTime('2017-01-01'),
                new \DateTime('2017-03-31'),
                $feeOptions,
                $this->buildMember('2015-08-01', NULL, '1970-05-13', [
                    $this->buildMemberFeeDiscount('2017-02-01', NULL)
                ]),
                2600,
                2600,
                1,
                'full2discount'
            ]
        ];
    }

    /**
     *
     * @param string $start
     *            Eintrittsdatum in den Verein
     * @param string $end
     *            Austrittsdatum aus dem Verein
     * @param string $birthday
     *            Geburtstag
     * @param array[MemberFeeDiscount] $discounts
     *            Zeiträume
     * @return
     */
    protected function buildMember($start, $end, $birthday, array $discounts = array())
    {
        $contact = new Contact();
        $contact->setBirthday(new \DateTime($birthday));
        $member = new Member();
        $member->setContact($contact);
        $member->setStartDate(new \DateTime($start));
        $member->setEndDate($end ? new \DateTime($end) : NULL);

        if (is_array($discounts)) {
            foreach ($discounts as $discount) {
                $member->addMemberFeeDiscount($discount);
            }
        }
        return $member;
    }

    /**
     * Erstellt Instanzen von MemberFeeDiscount
     *
     * @param string $start
     *            Startzeitpunkt der Ermäßigung
     * @param string $end
     *            Ende der Ermäßigung oder NULL
     */
    protected function buildMemberFeeDiscount($start, $end)
    {
        $discount = new MemberFeeDiscount();
        $discount->setStartDate(new \DateTime($start));
        $discount->setEndDate($end ? new \DateTime($end) : NULL);
        return $discount;
    }

    protected function getEMMockBuilder()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor();
    }
}
