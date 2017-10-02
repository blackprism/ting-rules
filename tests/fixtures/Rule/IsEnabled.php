<?php

namespace tests\fixtures\Rule;

use Aura\SqlQuery\Common\Select;
use Blackprism\TingRules\AbstractRule;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use CCMBenchmark\Ting\Repository\Metadata;

class IsEnabled extends AbstractRule
{
    /**
     * @var HydratorInterface
     */
    private $hydrator;

    public function getRule()
    {
        return "enabled = '1'";
    }

    public function getParameters(): array
    {
        return [];
    }

    public function applyQueryRule(Select $queryBuilder, Metadata $metadata, $rule, array $parameters = [])
    {
        return $queryBuilder
            ->where($rule)
            ->bindValues($parameters);
    }

    public function applyHydratorRule(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $hydrator;
    }

    /**
     *
     * @return HydratorInterface
     */
    public function getHydratorUsed()
    {
        return $this->hydrator;
    }
}
