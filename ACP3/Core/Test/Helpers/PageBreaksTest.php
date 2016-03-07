<?php
namespace ACP3\Core\Test\Helpers;


use ACP3\Core\Helpers\PageBreaks;
use ACP3\Core\Helpers\TableOfContents;
use ACP3\Core\Http\Request;
use ACP3\Core\Http\Request\ParameterBag;
use ACP3\Core\RouterInterface;
use ACP3\Core\SEO;

class PageBreaksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageBreaks
     */
    private $pageBreaks;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $seoMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $tocMock;

    protected function setUp()
    {
        $this->seoMock = $this->getMockBuilder(SEO::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $this->pageBreaks = new PageBreaks(
            $this->seoMock,
            $this->requestMock,
            $this->routerMock,
            $this->tocMock
        );
    }

    public function testSplitTextIntoPages()
    {
        $baseUrlPath = 'lorem/ipsum/dolor/id_1/';
        $this->setUpExpectations($baseUrlPath);
        $text = 'Foo Bar Baz<hr class="page-break">Lorem Ipsum Dolor<hr class="page-break">';

        $expected =  [
            'toc' => '',
            'text' => 'Foo Bar Baz',
            'next' => '/' . $baseUrlPath . 'page_2/',
            'previous' => '',
        ];

        $this->assertEquals($expected, $this->pageBreaks->splitTextIntoPages($text, $baseUrlPath));
    }

    /**
     * @param string $baseUrlPath
     */
    private function setUpExpectations($baseUrlPath)
    {
        $this->requestMock
            ->expects($this->atLeastOnce())
            ->method('getParameters')
            ->willReturn(new ParameterBag());

        $this->routerMock
            ->expects($this->atLeastOnce())
            ->method('route')
            ->with($baseUrlPath)
            ->willReturn('/' . $baseUrlPath);

        $this->tocMock
            ->expects($this->once())
            ->method('generateTOC')
            ->willReturn('');
    }
}
