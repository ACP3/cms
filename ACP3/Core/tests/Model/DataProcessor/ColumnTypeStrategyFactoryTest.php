<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model\DataProcessor;

use ACP3\Core\Model\CreateRawColumnTypeMockTrait;
use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;

class ColumnTypeStrategyFactoryTest extends \PHPUnit\Framework\TestCase
{
    use CreateRawColumnTypeMockTrait;

    /**
     * @var ColumnTypeStrategyFactory
     */
    private $columnTypeStrategyFactory;

    protected function setup(): void
    {
        $this->columnTypeStrategyFactory = new ColumnTypeStrategyFactory();
    }

    public function testGetStrategyWithValidColumnType()
    {
        $columnTypeMock = $this->getRawColumnTypeInstance($this);
        $this->columnTypeStrategyFactory->registerColumnType($columnTypeMock, 'raw');

        $this->assertInstanceOf(ColumnTypeStrategyInterface::class, $this->columnTypeStrategyFactory->getStrategy(ColumnTypes::COLUMN_TYPE_RAW));
    }

    public function testGetStrategyWithInvalidColumnType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->columnTypeStrategyFactory->getStrategy(ColumnTypes::COLUMN_TYPE_RAW);
    }
}
