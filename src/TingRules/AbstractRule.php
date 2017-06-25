<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;

abstract class AbstractRule implements Rule
{
    public function applyQueryRule(
        SelectInterface $queryBuilder,
        Metadata $metadata,
        string $rule,
        array $parameters = []
    ): SelectInterface {
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
