<?php

namespace Blackprism\TingRules\Rules;

use Aura\SqlQuery\Common\Select;
use Blackprism\TingRules\AbstractRule;
use Blackprism\TingRules\Rule;
use CCMBenchmark\Ting\Repository\Metadata;

class OrX extends AbstractRule
{
    /**
     * @var Rule[]
     */
    private $rules = [];

    public function __construct(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    public function orRule(Rule $orOtherRule)
    {
        $this->rules[] = $orOtherRule;

        return $this;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        $rules = array_map(
            function ($rule) {
                /** @var Rule $rule */
                return $rule->getRule();
            },
            $this->rules
        );
        return '(' . implode(') OR (', $rules) . ')';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function applyQueryRule(
        Select $queryBuilder,
        Metadata $metadata,
        $rule,
        array $parameters = []
    ): Select {
        return $this->rules[0]->applyQueryRule($queryBuilder, $metadata, $rule, $parameters);
    }
}
