<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\Select;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;

abstract class AbstractRule implements Rule
{
    /**
     * @param Select   $queryBuilder
     * @param Metadata $metadata
     * @param string   $rule
     * @param array    $parameters
     *
     * @return Select
     */
    public function applyQueryRule(Select $queryBuilder, Metadata $metadata, $rule, array $parameters = [])
    {
        return $queryBuilder;
    }

    /**
     * @param HydratorInterface $hydrator
     *
     * @return HydratorInterface
     */
    public function applyHydratorRule(HydratorInterface $hydrator)
    {
        return $hydrator;
    }

    /**
     * @param CollectionInterface $collection
     *
     * @return CollectionInterface
     */
    public function applyCollectionRule(CollectionInterface $collection)
    {
        return $collection;
    }
}
