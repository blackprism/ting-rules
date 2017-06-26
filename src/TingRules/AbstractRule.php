<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\Select;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;

abstract class AbstractRule implements Rule
{
    public function applyQueryRule(
        Select $queryBuilder,
        Metadata $metadata,
        $rule,
        array $parameters = []
    ): Select {
        return $queryBuilder;
    }

    public function applyHydratorRule(HydratorInterface $hydrator): HydratorInterface
    {
        return $hydrator;
    }

    public function applyCollectionRule(CollectionInterface $collection): CollectionInterface
    {
        return $collection;
    }
}
