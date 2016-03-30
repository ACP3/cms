<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Test\Core\Helper;

use ACP3\Modules\ACP3\Seo\Core\Helpers\PageBreaks;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class PageBreaksTest
 * @package ACP3\Modules\ACP3\Seo\Test\Core\Helper
 */
class PageBreaksTest extends \ACP3\Core\Test\Helpers\PageBreaksTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metaStatements;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->pageBreaks = new PageBreaks(
            $this->requestMock,
            $this->routerMock,
            $this->tocMock,
            $this->metaStatements
        );
    }

    protected function initializeMockObjects()
    {
        parent::initializeMockObjects();

        $this->metaStatements = $this->getMockBuilder(MetaStatements::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider splitTextIntoPagesDataProvider
     *
     * @param string $sourceText
     * @param int    $currentPage
     * @param string $currentPageText
     * @param string $baseUrlPath
     * @param string $nextPageUrl
     * @param string $prevPageUrl
     */
    public function testSplitTextIntoPages(
        $sourceText,
        $currentPage,
        $currentPageText,
        $baseUrlPath,
        $nextPageUrl,
        $prevPageUrl
    ) {
        $this->setUpMetaStatementsMockExpectations($nextPageUrl, $prevPageUrl);

        parent::testSplitTextIntoPages(
            $sourceText,
            $currentPage,
            $currentPageText,
            $baseUrlPath,
            $nextPageUrl,
            $prevPageUrl
        );
    }

    /**
     * @param string $nextPageUrl
     * @param string $prevPageUrl
     */
    private function setUpMetaStatementsMockExpectations($nextPageUrl, $prevPageUrl)
    {
        $this->metaStatements->expects($this->once())
            ->method('setNextPage')
            ->with($nextPageUrl)
            ->willReturnSelf();
        $this->metaStatements->expects($this->once())
            ->method('setPreviousPage')
            ->with($prevPageUrl)
            ->willReturnSelf();
    }
}
