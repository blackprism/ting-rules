<?php

namespace tests\units\Blackprism\TingRules;

use Aura\SqlQuery\Mysql\Select;
use Aura\SqlQuery\Quoter;
use CCMBenchmark\Ting\Query\QueryInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\HydratorAggregator;
use CCMBenchmark\Ting\Repository\HydratorSingleObject;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use mageekguy\atoum;
use tests\fixtures\Rule\IsEnabled;
use tests\fixtures\Rule\ReturnId;

class RulesApplier extends atoum
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var Select
     */
    private $select;

    /**
     * @var Repository
     */
    private $repository;

    private $getQueryParameters = [];

    /**
     * @internal Can't use beforeTestMethod https://github.com/atoum/atoum/issues/674
     */
    public function init()
    {

        $this->metadata = [
            'table'      => uniqid('table-'),
            'columns' => [
                [
                    'fieldName'  => uniqid('fieldId-'),
                    'columnName' => uniqid('columnId-'),
                    'type'       => 'int'
                ],
                [
                    'fieldName'  => uniqid('fieldName-'),
                    'columnName' => uniqid('columnName-'),
                    'type'       => 'string'
                ]
            ]
        ];

        $this->columns = array_column($this->metadata['columns'], 'columnName');

        $serializerFactory = $this->newMockInstance(SerializerFactoryInterface::class);

        $this->mockGenerator()->orphanize('__construct');
        $query = $this->newMockInstance(QueryInterface::class);
        $this->calling($query)->setParams->isFluent;
        $this->calling($query)->query = $this->newMockInstance(CollectionInterface::class);

        $this->select = $this->newMockInstance(Select::class, null, null, [new Quoter('`', '`'), '_1']);

        $this->mockGenerator()->orphanize('__construct');
        $this->repository = $this->newMockInstance(Repository::class);
        $this->calling($this->repository)->getQueryBuilder = $this->select;
        $that = $this;
        $this->calling($this->repository)->getQuery = function ($sql) use ($query, $that) {
            $that->getQueryParameters[0] = $sql;
            return $query;
        };
        $this->calling($this->repository)->getCollection = $this->newMockInstance(CollectionInterface::class);

        $metadataValues = $this->metadata;
        $this->calling($this->repository)->getMetadata = function () use ($serializerFactory, $metadataValues) {
            $metadata = new Metadata($serializerFactory);
            $metadata->setTable($metadataValues['table']);

            foreach ($metadataValues['columns'] as $column) {
                $metadata->addField($column);
            }

            return $metadata;
        };
        unset($metadataValues);

        $this->newTestedInstance($this->repository);
    }

    /**
     * @tags RulesApplier::rule
     */
    public function testRuleShouldReturnThis()
    {
        $this->init();

        $return = $this->testedInstance->rule($this->newMockInstance(IsEnabled::class));

        $this
            ->object($return)
            ->isTestedInstance();
    }

    /**
     * @tags RulesApplier::rule RulesApplier::getRules
     */
    public function testRuleShouldAddRule()
    {
        $this->init();

        $isEnabledRule = $this->newMockInstance(IsEnabled::class);
        $this->testedInstance->rule($isEnabledRule);

        $this
            ->array($this->testedInstance->getRules())
            ->isIdenticalTo([$isEnabledRule]);
    }

    /**
     * @tags RulesApplier::rule RulesApplier::getRules
     */
    public function testRuleWithIndexShouldReplaceRule()
    {
        $this->init();

        $this->testedInstance->rule($this->newMockInstance(IsEnabled::class));
        $returnIdRule = $this->newMockInstance(ReturnId::class);
        $this->testedInstance->rule($returnIdRule, 0);

        $this
            ->array($this->testedInstance->getRules())
            ->isIdenticalTo([$returnIdRule]);
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldCallApplyQueryRule()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($rule)
            ->call('applyQueryRule')
            ->once();
    }

    /**
     * @tags RulesApplier:apply
     */
    public function testApplyShouldCallGetQueryWithRuleApplied()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($this->repository)
            ->call('getQuery')
            ->once()
            ->string($this->getQueryParameters[0])
            ->matches("#^SELECT\s+" . implode(',\s+', $this->columns)
                . "\s+FROM\s+`" . $this->metadata['table']
                . "`\s+WHERE\s+enabled\s*=\s*'1'$#");
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldCallApplyHydratorRuleWithHydratorAggregator()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->object($rule->getHydratorUsed())
            ->isInstanceOf(HydratorAggregator::class);
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldCallApplyHydratorRuleWithMyHydrator()
    {
        $this->init();

        $hydrator = new HydratorSingleObject();
        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply($hydrator);

        $this
            ->object($rule->getHydratorUsed())
            ->isIdenticalTo($hydrator);
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldCallApplyCollectionRule()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($rule)
            ->call('applyCollectionRule')
            ->once();
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldReturnCollectionInterface()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $collection = $this->testedInstance->apply();

        $this
            ->object($collection)
            ->isInstanceOf(CollectionInterface::class);
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldBuildQueryFromWithMetadata()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($this->select)
            ->call('from')
            ->withIdenticalArguments($this->metadata['table'])
            ->once();
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldBuildQueryColumnsWithMetadata()
    {
        $this->init();

        $rule = $this->newMockInstance(IsEnabled::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($this->select)
            ->call('cols')
            ->withIdenticalArguments(array_column($this->metadata['columns'], 'columnName'))
            ->once();
    }

    /**
     * @tags RulesApplier::apply
     */
    public function testApplyShouldBuildQueryColumnsWithRuleColumns()
    {
        $this->init();

        $rule = $this->newMockInstance(ReturnId::class);

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($this->select)
            ->call('cols')
            ->withIdenticalArguments(['id'])
            ->once();
    }
}
