<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;


use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\View\Block\Context\DataGridBlockContext;
use ACP3\Core\View\Block\DataGridBlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDataGridBlockTest extends AbstractBlockTest
{
    /**
     * @var DataGridBlockInterface
     */
    protected $block;
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;
    /**
     * @var DataGrid|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataGrid;

    protected function setUpMockObjects()
    {
        $this->context = $this->getMockBuilder(DataGridBlockContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'set', 'has', 'initialized', 'getParameter', 'setParameter', 'hasParameter'])
            ->getMock();

        $this->dataGrid = $this->getMockBuilder(DataGrid::class)
            ->disableOriginalConstructor()
            ->setMethods(['setIdentifier', 'addColumn', 'render'])
            ->getMock();

        $this->dataGrid->expects($this->once())
            ->method('setIdentifier')
            ->willReturnSelf();

        $this->dataGrid->expects($this->atLeastOnce())
            ->method('addColumn')
            ->willReturnSelf();

        $this->dataGrid->expects($this->once())
            ->method('render')
            ->willReturn([]);

        $this->container->expects($this->atLeastOnce())
            ->method('get')
            ->with('core.helpers.data_grid')
            ->willReturn($this->dataGrid);

        $this->context->expects($this->atLeastOnce())
            ->method('getContainer')
            ->willReturn($this->container);
    }
}
