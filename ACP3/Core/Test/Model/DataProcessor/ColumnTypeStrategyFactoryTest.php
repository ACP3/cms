<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor;

use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Core\Model\DataProcessor\ColumnTypeStrategyFactory;
use ACP3\Core\Test\Model\CreateRawColumnTypeMockTrait;

class ColumnTypeStrategyFactoryTest extends \PHPUnit_Framework_TestCase
{
    use CreateRawColumnTypeMockTrait;

    /**
     * @var ColumnTypeStrategyFactory
     */
    private $columnTypeStrategyFactory;

    protected function setUp()
    {
        $this->columnTypeStrategyFactory = new ColumnTypeStrategyFactory();
    }

    public function testGetStrategyWithValidColumnType()
    {
        $columnTypeMock = $this->getRawColumnTypeInstance($this);
        $this->columnTypeStrategyFactory->registerColumnType($columnTypeMock, 'raw');

        $this->assertInstanceOf(ColumnTypeStrategyInterface::class, $this->columnTypeStrategyFactory->getStrategy(ColumnTypes::COLUMN_TYPE_RAW));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetStrategyWithInvalidColumnType()
    {
        $this->columnTypeStrategyFactory->getStrategy(ColumnTypes::COLUMN_TYPE_RAW);
    }
}
