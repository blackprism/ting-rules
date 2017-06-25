# Ting - rules system to build query

Still in progress. You SHOULD NOT use this package until release 1.0.

## Build a rule

    <?php
    
    namespace AppBundle\Domain\Feed\Rules;
    
    use Aura\SqlQuery\Common\SelectInterface;
    use Blackprism\TingRules\AbstractRule;
    
    class IsEnabled extends AbstractRule
    {
        public function getRule(): string
        {
            return "feed.enabled = '1'";
        }
    
        public function getParameters(): array
        {
            return [];
        }
    
        public function applyQueryRule(SelectInterface $queryBuilder, string $rule, array $parameters = []): SelectInterface
        {
            return $queryBuilder
                ->where($rule)
                ->bindValues($parameters);
        }
    }


## Use your rule

    <?php
    $rulesApplier = new RulesApplier($feedRepository);
    $rulesApplier->rules([
        new IsEnabled()
    ]);
    $feeds = $rulesApplier->apply();
