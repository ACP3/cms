<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\View\Block;

use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Pagination;
use ACP3\Core\View\Block\Context\ListingBlockContext;
use ACP3\Core\View\Block\ListingBlockInterface;

abstract class AbstractListingBlockTest extends AbstractBlockTest
{
    /**
     * @var ListingBlockInterface
     */
    protected $block;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $resultsPerPage = $this->getMockBuilder(ResultsPerPage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultsPerPage->expects($this->once())
            ->method('getResultsPerPage')
            ->with($this->getExpectedModuleName())
            ->willReturn(20);

        $this->context->expects($this->once())
            ->method('getResultsPerPage')
            ->willReturn($resultsPerPage);

        $pagination = $this->getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pagination->expects($this->once())
            ->method('setResultsPerPage')
            ->willReturnSelf();

        $pagination->expects($this->once())
            ->method('setTotalResults')
            ->willReturnSelf();

        $this->context->expects($this->once())
            ->method('getPagination')
            ->willReturn($pagination);
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockFQCN(): string
    {
        return ListingBlockContext::class;
    }

    /**
     * @inheritdoc
     */
    protected function getContextMockMethods(): array
    {
        return ['getView', 'getBreadcrumb', 'getTitle', 'getTranslator', 'getResultsPerPage', 'getPagination'];
    }

    /**
     * @return string
     */
    abstract protected function getExpectedModuleName(): string;
}
