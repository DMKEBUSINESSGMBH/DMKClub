<?php

namespace DMKClub\Bundle\BasicsBundle\Datasource\ORM;

use Oro\Bundle\BatchBundle\ORM\Query\ResultIterator\IdentifierWithoutOrderByIterationStrategy;
use Oro\Bundle\DataGridBundle\Datasource\Orm\IterableResult;

/**
 * Ermöglicht die Iteration über komplexe Datagrids. Dabei wird aber auf die Sortierung verzichtet.
 *
 */
class NoOrderingIterableResult extends IterableResult
{
    /**
     * {@inheritdoc}
     */
    protected function getIterationStrategy()
    {
        if (null === $this->iterationStrategy) {
            $this->iterationStrategy = new IdentifierWithoutOrderByIterationStrategy();
        }

        return $this->iterationStrategy;
    }
}
