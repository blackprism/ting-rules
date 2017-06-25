<?php

namespace Blackprism\TingRules\Rules;

use Aura\SqlQuery\Common\SelectInterface;
use Blackprism\TingRules\AbstractRule;
use Blackprism\TingRules\Rule;

class NotX extends AbstractRule
{
    private $ruleToNegate;

    public function __construct(Rule $ruleToNegate)
    {
        $this->ruleToNegate = $ruleToNegate;
    }

    public function getRule(): string
    {
        return 'NOT (' . $this->ruleToNegate->getRule() . ')';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function applyQueryRule(SelectInterface $queryBuilder, string $rule, array $parameters = []): SelectInterface
    {
        return $this->ruleToNegate->applyQueryRule($queryBuilder, $rule, $parameters);
    }
}
