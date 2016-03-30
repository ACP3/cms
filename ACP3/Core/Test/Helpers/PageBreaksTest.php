<?php
namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Helpers\TableOfContents;
use ACP3\Core\Http\Request;
use ACP3\Core\Http\Request\ParameterBag;
use ACP3\Core\RouterInterface;

class PageBreaksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageBreaks
     */
    protected $pageBreaks;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tocMock;

    protected function setUp()
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
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParameters'])
            ->getMock();
        $this->routerMock = $this->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->tocMock = $this->getMockBuilder(TableOfContents::class)
            ->disableOriginalConstructor()
            ->getMock();
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
                ''
            ],
            'two_pages_first_page_visible' => [
                'Foo Bar Baz<hr class="page-break">Lorem Ipsum Dolor<hr class="page-break">',
                1,
                'Foo Bar Baz',
                $baseUrlPath,
                '/' . $baseUrlPath . 'page_2/',
                ''
            ],
            'two_pages_last_page_visible' => [
                'Foo Bar Baz<hr class="page-break">Lorem Ipsum Dolor<hr class="page-break">',
                2,
                'Lorem Ipsum Dolor',
                $baseUrlPath,
                '',
                '/' . $baseUrlPath
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

        $this->assertEquals($expected, $this->pageBreaks->splitTextIntoPages($sourceText, $baseUrlPath));
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
            ->willReturn(new ParameterBag(['page' => $currentPage]));

        $this->routerMock
            ->expects($this->any())
            ->method('route')
            ->with($baseUrlPath)
            ->willReturn('/' . $baseUrlPath);

        $this->tocMock
            ->expects($this->once())
            ->method('generateTOC')
            ->willReturn('');
    }
}
