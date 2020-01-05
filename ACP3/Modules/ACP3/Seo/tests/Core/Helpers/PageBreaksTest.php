<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Helpers;

use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class PageBreaksTest extends \ACP3\Core\Helpers\PageBreaksTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
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

        $this->metaStatements = $this->createMock(MetaStatements::class);
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
