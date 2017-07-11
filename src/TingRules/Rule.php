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

    /**
     * @return array
     */
    public function getParameters();

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
    public function applyQueryRule(Select $queryBuilder, Metadata $metadata, $rule, array $parameters = []);

    /**
     * @param HydratorInterface $hydrator
     *
     * @throws \RuntimeException
     *
     * @return HydratorInterface
     */
    public function applyHydratorRule(HydratorInterface $hydrator);

    /**
     * @param CollectionInterface $collection
     *
     * @throws \RuntimeException
     *
     * @return CollectionInterface
     */
    public function applyCollectionRule(CollectionInterface $collection);
}
