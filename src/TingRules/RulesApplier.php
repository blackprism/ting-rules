<?php

namespace Blackprism\TingRules;

use Aura\SqlQuery\Common\Select;
use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Query\Query;
use CCMBenchmark\Ting\Query\QueryException;
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
     * @var array
     */
    private $rules = [];

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Rule $rule
     * @param string $identifier
     *
     * @return $this
     */
    public function rule(Rule $rule, $identifier = null)
    {
        if ($identifier !== null) {
            $this->rules[$identifier] = $rule;
            return $this;
        }

        $this->rules[] = $rule;
        return $this;
    }

    private function applyQueryRules(SelectInterface $queryBuilder): SelectInterface
    {
        foreach ($this->rules as $rule) {
            $queryBuilder = $rule->applyQueryRule($queryBuilder, $rule->getRule(), $rule->getParameters());
        }

        return $queryBuilder;
    }

    private function applyHydrators(HydratorInterface $hydrator): HydratorInterface
    {
        foreach ($this->rules as $rule) {
            $hydrator = $rule->applyHydratorRule($hydrator);
        }

        return $hydrator;
    }

    private function applyFinalize(CollectionInterface $collection)
    {
        foreach ($this->rules as $rule) {
            $collection = $rule->applyFinalizeRule($collection);
        }

        return $collection;
    }

    private function getColumns(Metadata $metadata): array
    {
        $columns = [];
        foreach ($metadata->getFields() as $field) {
            $columns[] = $field['columnName'];
        }

        return $columns;
    }

    private function getQueryForSelect(SelectInterface $select)
    {
        return $this->repository
            ->getQuery($select->getStatement());
    }

    private function buildQueryFromSelect(SelectInterface $select): Query
    {
        return $this->getQueryForSelect($select)->setParams($select->getBindValues());
    }

    private function getHydrator(HydratorInterface $hydrator = null): HydratorInterface
    {
        if ($hydrator !== null) {
            return $hydrator;
        }

        $hydrator = new HydratorAggregator();

        $hydrator->callableIdIs(function ($result) {
            return mt_rand();
        });

        $hydrator->callableDataIs(function ($result) {
            return $result;
        });

        return $hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     *
     * @throws Exception
     * @throws QueryException
     *
     * @return mixed
     */
    public function apply(HydratorInterface $hydrator = null)
    {
        $metadata = $this->repository->getMetadata();
        $hydrator = $this->getHydrator($hydrator);

        /** @var SelectInterface $select */
        $select = $this->repository->getQueryBuilder(Repository::QUERY_SELECT);
        $select->from($metadata->getTable());

        $select = $this->applyQueryRules($select);

        if ($select instanceof Select && $select->hasCols() === false) {
            $select->cols($this->getColumns($metadata));
        }

        $hydrator = $this->applyHydrators($hydrator);
        $collection = $this->buildQueryFromSelect($select)->query($this->repository->getCollection($hydrator));

        return $this->applyFinalize($collection);
    }
}
