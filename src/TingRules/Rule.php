<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\SelectInterface;
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
     * @param SelectInterface $queryBuilder
     * @param Metadata        $metadata
     * @param string          $rule
     * @param array           $parameters
     *
     * @throws \RuntimeException
     *
     * @return SelectInterface
     */
    public function applyQueryRule(
        SelectInterface $queryBuilder,
        Metadata $metadata,
        string $rule,
        array $parameters = []
    ): SelectInterface;

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
