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

    /**
     * @param Rule $rule
     */
    public function __construct(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param Rule $orOtherRule
     *
     * @return $this
     */
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
            /** @return string */
            function ($rule) {
                /** @var Rule $rule */
                return $rule->getRule();
            },
            $this->rules
        );
        return '(' . implode(') OR (', $rules) . ')';
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return [];
    }

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
    ): Select {
        return $this->rules[0]->applyQueryRule($queryBuilder, $metadata, $rule, $parameters);
    }
}
