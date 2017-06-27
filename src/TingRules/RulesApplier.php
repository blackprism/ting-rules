<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\Select;
use CCMBenchmark\Ting\Query\Query;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorAggregator;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\Repository;

class RulesApplier
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var array<int, Rule>
     */
    private $rules = [];

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Rule $rule
     * @param int  $index
     *
     * @return $this
     */
    public function rule(Rule $rule, $index = null)
    {
        if ($index !== null) {
            $this->rules[(int) $index] = $rule;
            return $this;
        }

        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param Select   $queryBuilder
     * @param Metadata $metadata
     *
     * @throws \RuntimeException
     *
     * @return Select
     */
    private function applyQueryRule(Select $queryBuilder, Metadata $metadata): Select
    {
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            $queryBuilder = $rule->applyQueryRule($queryBuilder, $metadata, $rule->getRule(), $rule->getParameters());
        }

        return $queryBuilder;
    }

    /**
     * @param HydratorInterface $hydrator
     *
     * @throws \RuntimeException
     *
     * @return HydratorInterface
     */
    private function applyHydratorRule(HydratorInterface $hydrator): HydratorInterface
    {
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            $hydrator = $rule->applyHydratorRule($hydrator);
        }

        return $hydrator;
    }

    /**
     * @param CollectionInterface $collection
     *
     * @throws \RuntimeException
     *
     * @return CollectionInterface
     */
    private function applyCollectionRule(CollectionInterface $collection)
    {
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            $collection = $rule->applyCollectionRule($collection);
        }

        return $collection;
    }

    /**
     * @param Metadata $metadata
     *
     * @return string[int]
     */
    private function getColumns(Metadata $metadata): array
    {
        $columns = [];

        /** @var array $field */
        foreach ($metadata->getFields() as $field) {
            $columns[] = (string) $field['columnName'];
        }

        return $columns;
    }

    /**
     * @param Select $select
     *
     * @return Query
     */
    private function getQueryForSelect(Select $select)
    {
        return $this->repository->getQuery($select->getStatement());
    }

    /**
     * @param Select $select
     *
     * @return Query
     */
    private function buildQueryFromSelect(Select $select): Query
    {
        return $this->getQueryForSelect($select)->setParams($select->getBindValues());
    }

    /**
     * @param HydratorInterface|null $hydrator
     *
     * @return HydratorInterface
     */
    private function getHydrator(HydratorInterface $hydrator = null): HydratorInterface
    {
        if ($hydrator !== null) {
            return $hydrator;
        }

        $hydrator = new HydratorAggregator();

        $hydrator->callableIdIs(
            /** @return string */
            function () {
                return (string) mt_rand();
            }
        );

        $hydrator->callableDataIs(
            /** @return mixed */
            function ($result) {
                return $result;
            }
        );

        return $hydrator;
    }

    /**
     * @param HydratorInterface|null $hydrator
     *
     * @throws \RuntimeException
     *
     * @return CollectionInterface
     */
    public function apply(HydratorInterface $hydrator = null): CollectionInterface
    {
        $metadata = $this->repository->getMetadata();
        $hydrator = $this->getHydrator($hydrator);

        /** @var Select $select */
        $select = $this->repository->getQueryBuilder(Repository::QUERY_SELECT);
        $select->from($metadata->getTable());

        $select = $this->applyQueryRule($select, $metadata);

        if ($select instanceof Select && $select->hasCols() === false) {
            $select->cols($this->getColumns($metadata));
        }

        $hydrator = $this->applyHydratorRule($hydrator);
        $collection = $this->buildQueryFromSelect($select)->query($this->repository->getCollection($hydrator));

        return $this->applyCollectionRule($collection);
    }
}
