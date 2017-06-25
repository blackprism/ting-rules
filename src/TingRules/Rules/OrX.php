<?php

namespace Blackprism\TingRules\Rules;

use Aura\SqlQuery\Common\SelectInterface;
use Blackprism\TingRules\AbstractRule;
use Blackprism\TingRules\Rule;

class OrX extends AbstractRule
{
    private $rules = [];

    public function __construct(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    public function or(Rule $orOtherRule)
    {
        $this->rules[] = $orOtherRule;

        return $this;
    }

    public function getRule(): string
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

    public function applyQueryRule(SelectInterface $queryBuilder, string $rule, array $parameters = []): SelectInterface
    {
        return $this->rules[0]->applyQueryRule($queryBuilder, $rule, $parameters);
    }
}
