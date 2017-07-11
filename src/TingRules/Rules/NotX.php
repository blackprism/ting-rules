<?php

namespace Blackprism\TingRules\Rules;

use Aura\SqlQuery\Common\Select;
use Blackprism\TingRules\AbstractRule;
use Blackprism\TingRules\Rule;
use CCMBenchmark\Ting\Repository\Metadata;

class NotX extends AbstractRule
{
    /**
     * @var Rule
     */
    private $ruleToNegate;

    /**
     * @param Rule $ruleToNegate
     */
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

    /**
     *
     * @return array
     */
    public function getParameters()
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
    public function applyQueryRule(Select $queryBuilder, Metadata $metadata, $rule, array $parameters = [])
    {
        return $this->ruleToNegate->applyQueryRule($queryBuilder, $metadata, $rule, $parameters);
    }
}
