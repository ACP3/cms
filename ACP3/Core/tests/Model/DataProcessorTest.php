<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Model;

class DataProcessorTest extends \PHPUnit\Framework\TestCase
{
    use CreateRawColumnTypeMockTrait;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $columnTypeStrategyFactoryMock;
    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    protected function setUp()
    {
        $this->columnTypeStrategyFactoryMock = $this->createMock(DataProcessor\ColumnTypeStrategyFactory::class);
        $this->dataProcessor = new DataProcessor($this->columnTypeStrategyFactoryMock);
    }

    public function testProcessColumnData()
    {
        $columnData = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
            'baz' => 'Dolor',
        ];

        $columnConstraints = [
            'foo' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'bar' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
        ];

        $this->setUpColumnTypeStrategyFactoryExpectations();

        $expected = [
            'foo' => 'Lorem',
            'bar' => 'Ipsum',
        ];
        $this->assertEquals($expected, $this->dataProcessor->escape($columnData, $columnConstraints));
    }

    private function setUpColumnTypeStrategyFactoryExpectations()
    {
        $columnTypeMock = $this->getRawColumnTypeInstance($this);

        $columnTypeMock
            ->expects($this->exactly(2))
            ->method('doEscape')
            ->with($this->logicalOr('Lorem', 'Ipsum'))
            ->willReturnOnConsecutiveCalls('Lorem', 'Ipsum');

        $this->columnTypeStrategyFactoryMock
            ->expects($this->exactly(2))
            ->method('getStrategy')
            ->with(DataProcessor\ColumnTypes::COLUMN_TYPE_RAW)
            ->willReturn($columnTypeMock);
    }
}
