<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core\Http\Request;
use ACP3\Core\Router\RouterInterface;

class PageBreaksTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PageBreaks
     */
    protected $pageBreaks;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $routerMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $tocMock;

    protected function setup(): void
    {
        $this->initializeMockObjects();

        $this->pageBreaks = new PageBreaks(
            $this->requestMock,
            $this->routerMock,
            $this->tocMock
        );
    }

    protected function initializeMockObjects()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->tocMock = $this->createMock(TableOfContents::class);
    }

    public function splitTextIntoPagesDataProvider()
    {
        $baseUrlPath = 'lorem/ipsum/dolor/id_1/';

        return [
            'single_page' => [
                'Foo Bar Baz',
                1,
                'Foo Bar Baz',
                $baseUrlPath,
                '',
                '',
            ],
            'two_pages_first_page_visible' => [
                'Foo Bar Baz<hr class="page-break">Lorem Ipsum Dolor<hr class="page-break">',
                1,
                'Foo Bar Baz',
                $baseUrlPath,
                '/' . $baseUrlPath . 'page_2/',
                '',
            ],
            'two_pages_last_page_visible' => [
                'Foo Bar Baz<hr class="page-break">Lorem Ipsum Dolor<hr class="page-break">',
                2,
                'Lorem Ipsum Dolor',
                $baseUrlPath,
                '',
                '/' . $baseUrlPath,
            ],
        ];
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
        $this->setUpExpectations($currentPage, $baseUrlPath);

        $expected = [
            'toc' => '',
            'text' => $currentPageText,
            'next' => $nextPageUrl,
            'previous' => $prevPageUrl,
        ];

        self::assertEquals($expected, $this->pageBreaks->splitTextIntoPages($sourceText, $baseUrlPath));
    }

    /**
     * @param int    $currentPage
     * @param string $baseUrlPath
     */
    private function setUpExpectations($currentPage, $baseUrlPath)
    {
        $this->requestMock
            ->expects($this->any())
            ->method('getParameters')
            ->willReturn(new \Symfony\Component\HttpFoundation\ParameterBag(['page' => $currentPage]));

        $this->routerMock
            ->expects($this->any())
            ->method('route')
            ->with($baseUrlPath)
            ->willReturn('/' . $baseUrlPath);

        $this->tocMock
            ->expects(self::once())
            ->method('generateTOC')
            ->willReturn('');
    }
}
