<?php

namespace tests\units\Blackprism\TingRules;

use Aura\SqlQuery\QueryFactory;
use Blackprism\TingRules\AbstractRule;
use CCMBenchmark\Ting\Query\QueryInterface;
use CCMBenchmark\Ting\Repository\CollectionInterface;
use CCMBenchmark\Ting\Repository\Metadata;
use CCMBenchmark\Ting\Repository\Repository;
use CCMBenchmark\Ting\Serializer\SerializerFactoryInterface;
use mageekguy\atoum;
use function uniqid;

class RulesApplier extends atoum
{
    /**
     * @internal Can't use beforeTestMethod https://github.com/atoum/atoum/issues/674
     */
    public function init()
    {
        $serializerFactory = $this->newMockInstance(SerializerFactoryInterface::class);

        $this->mockGenerator()->orphanize('__construct');
        $query = $this->newMockInstance(QueryInterface::class);
        $this->calling($query)->setParams->isFluent;
        $this->calling($query)->query = $this->newMockInstance(CollectionInterface::class);

        $this->mockGenerator()->orphanize('__construct');
        $repository = $this->newMockInstance(Repository::class);
        $this->calling($repository)->getQueryBuilder = (new QueryFactory('mysql'))->newSelect();
        $this->calling($repository)->getQuery = $query;
        $this->calling($repository)->getCollection = $this->newMockInstance(CollectionInterface::class);
        $this->calling($repository)->getMetadata =
            (new Metadata($serializerFactory))
                ->setTable(uniqid('table-'))
                ->addField([
                    'fieldName'  => uniqid('fieldName-'),
                    'columnName' => uniqid('columnName-'),
                    'type'       => 'string'
                ]);

        $this->newTestedInstance($repository);
    }

    public function testRuleShouldReturnThis()
    {
        $this->init();

        $return = $this->testedInstance->rule($this->newMockInstance(AbstractRule::class));

        $this
            ->object($return)
            ->isTestedInstance();
    }

    public function testApplyShouldCallApplyQueryRule()
    {
        $this->init();

        $rule = $this->newMockInstance(AbstractRule::class);
        $this->calling($rule)->getRule = 'enabled = 1';
        $this->calling($rule)->getParameters = [];

        $this->testedInstance->rule($rule);
        $this->testedInstance->apply();

        $this
            ->mock($rule)
            ->call('applyQueryRule')
            ->once();
    }
}
