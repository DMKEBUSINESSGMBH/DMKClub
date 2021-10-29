<?php
namespace DMKClub\Bundle\MemberBundle\Tests\Unit\Entity;

use DMKClub\Bundle\MemberBundle\Entity\MemberBilling;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeePosition;
use PHPUnit\Framework\TestCase;

class MemberBillingTest extends TestCase
{

    /**
     * @dataProvider getLabels
     */
    public function testGetPositionLabelMap($labels)
    {
        $billing = new MemberBilling();
        $billing->setPositionLabels($labels);
        $labels = $billing->getPositionLabelMap();

        $this->assertEquals(3, count($labels));

        $this->assertEquals('Beitrag vom [STARTDATE] bis [ENDDATE]', $labels[MemberFeePosition::FLAG_FEE]);
        $this->assertEquals('Einmalige Aufnahmegebühr', $labels[MemberFeePosition::FLAG_ADMISSON]);
        $this->assertEquals('Korrektur Ihres Beitrags', $labels[MemberFeePosition::FLAG_CORRECTION]);
    }
    public function getLabels()
    {
        return [
            [
                "FEE Beitrag vom [STARTDATE] bis [ENDDATE]
ADMISSION Einmalige Aufnahmegebühr
FEECORRECTION Korrektur Ihres Beitrags
",
            ],
            [
                "FEE Beitrag vom [STARTDATE] bis [ENDDATE] ADMISSION Einmalige Aufnahmegebühr FEECORRECTION Korrektur Ihres Beitrags",
            ],
            [
                "FEE Beitrag vom [STARTDATE] bis [ENDDATE] ADMISSION Einmalige Aufnahmegebühr
FEECORRECTION Korrektur Ihres Beitrags",
            ],
        ];
    }
}
