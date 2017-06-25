# Ting - rules system to build query

Still in progress. You SHOULD NOT use this package until release 1.0.

## Build a rule

### Basic rule

```php
<?php

namespace AppBundle\Domain\Feed\Rules;

use Aura\SqlQuery\Common\SelectInterface;
use Blackprism\TingRules\AbstractRule;
use CCMBenchmark\Ting\Repository\Metadata;

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

    public function applyQueryRule(
        SelectInterface $queryBuilder,
        Metadata $metadata,
        string $rule,
        array $parameters = []
    ): SelectInterface {
        return $queryBuilder
            ->where($rule)
            ->bindValues($parameters);
    }
}
```

### Use your rule

```php
<?php
$rulesApplier = new RulesApplier($feedRepository);
$rulesApplier->rule(new IsEnabled());
$feeds = $rulesApplier->apply();
```

### Advanced rule

```php
<?php

namespace AppBundle\Domain\Feed\Rules;

use Aura\SqlQuery\Common\SelectInterface;
use CCMBenchmark\Ting\Repository\HydratorAggregator;
use CCMBenchmark\Ting\Repository\HydratorInterface;
use Blackprism\TingRules\AbstractRule;
use CCMBenchmark\Ting\Repository\Metadata;

class WithArticle extends AbstractRule
{
    public function getRule(): string
    {
        return '';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function applyQueryRule(
        SelectInterface $queryBuilder,
        Metadata $metadata,
        string $rule,
        array $parameters = []
    ): SelectInterface {
        return $queryBuilder
            ->cols(['*'])
            ->leftJoin('article', 'feed.id = article.feed_id');
    }

    public function applyHydratorRule(HydratorInterface $hydrator): HydratorInterface
    {
        if ($hydrator instanceof HydratorAggregator === false) {
            throw new \RuntimeException(
                "Can't apply " . self::class . " rule because Hydrator is not instance of HydratorAggregator"
            );
        }

        /** @var HydratorAggregator $hydrator */
        $hydrator->callableIdIs(function ($result) {
            return $result['feed']->getId();
        });

        $hydrator->callableDataIs(function ($result) {
            return $result['article'];
        });

        $hydrator->callableFinalizeAggregate(function ($result, $articles) {
            $result['feed']->setArticles($articles);
            return $result['feed'];
        });

        return $hydrator;
    }
}
```

### Even with advanced rule it's still easy to use

```php
<?php
$rulesApplier = new RulesApplier($feedRepository);
$rulesApplier->rule(new WithArticle());
$feeds = $rulesApplier->apply();
```
