<?php
namespace DMKClub\Bundle\BasicsBundle\Utility;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Oro\Bundle\DataGridBundle\Datasource\ResultRecordInterface;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface;

class Datagrids
{

    public function cent2float($gridName, $keyName, $node)
    {
        $dataName = $node[PropertyInterface::DATA_NAME_KEY];

        return function (ResultRecordInterface $record) use ($dataName) {

            $result = $record->getValue($dataName);
            $result = $result / 100;
            return $result;
        };
    }
}
