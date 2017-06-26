<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\Select;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;

interface Rule
{
    /**
     * @return string
     */
    public function getRule();

    public function getParameters(): array;

    /**
     * @param Select   $queryBuilder
     * @param Metadata $metadata
     * @param string   $rule
     * @param array    $parameters
     *
     * @throws \RuntimeException
     *
     * @return Select
     */
    public function applyQueryRule(
        Select $queryBuilder,
        Metadata $metadata,
        $rule,
        array $parameters = []
    ): Select;

    /**
     * @param HydratorInterface $hydrator
     *
     * @throws \RuntimeException
     *
     * @return HydratorInterface
     */
    public function applyHydratorRule(HydratorInterface $hydrator): HydratorInterface;

    /**
     * @param CollectionInterface $collection
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    public function applyCollectionRule(CollectionInterface $collection): CollectionInterface;
}
