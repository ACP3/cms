<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Model\DataProcessor;


use ACP3\Core\Model\DataProcessor\ColumnType\ColumnTypeStrategyInterface;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Core\Model\DataProcessor\ColumnTypeStrategyFactory;
use ACP3\Core\Test\Model\CreateRawColumnTypeMockTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ColumnTypeStrategyFactoryTest extends \PHPUnit_Framework_TestCase
{
    use CreateRawColumnTypeMockTrait;

    /**
     * @var ColumnTypeStrategyFactory
     */
    private $columnTypeStrategyFactory;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;

    protected function setUp()
    {
        $this->containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->setMethods(['set', 'get', 'has', 'initialized', 'setParameter', 'getParameter', 'hasParameter'])
            ->getMock();

        $this->columnTypeStrategyFactory = new ColumnTypeStrategyFactory($this->containerMock);
    }

    public function testGetStrategyWithValidColumnType()
    {
        $this->setUpContainerMockExpectations();

        $this->assertInstanceOf(ColumnTypeStrategyInterface::class, $this->columnTypeStrategyFactory->getStrategy(ColumnTypes::COLUMN_TYPE_RAW));
    }

    private function setUpContainerMockExpectations()
    {
        $serviceId = 'core.model.column_type.' . ColumnTypes::COLUMN_TYPE_RAW . '_column_type_strategy';

        $this->containerMock
            ->expects($this->once())
            ->method('get')
            ->with($serviceId)
            ->willReturn($this->getRawColumnTypeInstance($this));
    }
}
