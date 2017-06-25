<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorInterface;

interface Rule
{
    /**
     * @return string
     */
    public function getRule();

    public function getParameters(): array;

    public function applyQueryRule(
        SelectInterface $queryBuilder,
        string $rule,
        array $parameters = []
    ): SelectInterface;

    public function applyHydratorRule(HydratorInterface $hydrator): HydratorInterface;

    public function applyFinalizeRule(CollectionInterface $collection): CollectionInterface;
}
