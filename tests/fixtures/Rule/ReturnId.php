<?php

namespace tests\fixtures\Rule;

use Aura\SqlQuery\Common\Select;
use Blackprism\TingRules\AbstractRule;
use CCMBenchmark\Ting\Repository\Metadata;

class ReturnId extends AbstractRule
{
    public function getRule(): string
    {
        return '';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function applyQueryRule(Select $queryBuilder, Metadata $metadata, $rule, array $parameters = [])
    {
        return $queryBuilder
            ->cols(['id'])
            ->bindValues($parameters);
    }
}
