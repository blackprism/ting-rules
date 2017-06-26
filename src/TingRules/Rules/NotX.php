<?php

namespace Blackprism\TingRules\Rules;

use Aura\SqlQuery\Common\Select;
use Blackprism\TingRules\AbstractRule;
use Blackprism\TingRules\Rule;
use CCMBenchmark\Ting\Repository\Metadata;

class NotX extends AbstractRule
{
    private $ruleToNegate;

    public function __construct(Rule $ruleToNegate)
    {
        $this->ruleToNegate = $ruleToNegate;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return 'NOT (' . $this->ruleToNegate->getRule() . ')';
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
        return $this->ruleToNegate->applyQueryRule($queryBuilder, $metadata, $rule, $parameters);
    }
}
