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

    /**
     * @param SelectInterface $queryBuilder
     * @param string          $rule
     * @param array           $parameters
     *
     * @throws \RuntimeException
     *
     * @return SelectInterface
     */
    public function applyQueryRule(
        SelectInterface $queryBuilder,
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
    public function applyFinalizeRule(CollectionInterface $collection);
}
