<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;

use ACP3\Core\Helpers\DataGrid;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\View\Block\Context\DataGridBlockContext;
use ACP3\Core\View\Block\DataGridBlockInterface;
use Psr\Container\ContainerInterface;

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
     * @var ResultsPerPage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultsPerPage;
    /**
     * @var DataGrid|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataGrid;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'has'])
            ->getMockForAbstractClass();

        $this->resultsPerPage = $this->getMockBuilder(ResultsPerPage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResultsPerPage'])
            ->getMock();

        $this->resultsPerPage->expects($this->atLeastOnce())
            ->method('getResultsPerPage')
            ->willReturn(10);

        $this->dataGrid = $this->getMockBuilder(DataGrid::class)
            ->disableOriginalConstructor()
            ->setMethods(['setOptions', 'addColumn', 'render'])
            ->getMock();

        $this->dataGrid->expects($this->once())
            ->method('setOptions')
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

        $this->context->expects($this->atLeastOnce())
            ->method('getResultsPerPage')
            ->willReturn($this->resultsPerPage);
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockFQCN(): string
    {
        return DataGridBlockContext::class;
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockMethods(): array
    {
        return ['getView', 'getBreadcrumb', 'getTitle', 'getTranslator', 'getContainer', 'getResultsPerPage'];
    }
}
