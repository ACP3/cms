<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Assets;

use ACP3\Core\Assets\PageCssClasses;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Http\Request;

class PageCssClassesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PageCssClasses
     */
    private $pageCssClasses;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $titleMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $stringFormatterMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->pageCssClasses = new PageCssClasses(
            $this->stringFormatterMock,
            $this->titleMock,
            $this->requestMock
        );
    }

    private function setUpMockObjects()
    {
        $this->titleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stringFormatterMock = $this->getMockBuilder(StringFormatter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetModule()
    {
        $this->requestMock->expects($this->once())
            ->method('getModule')
            ->willReturn('foo');

        $this->assertEquals('foo', $this->pageCssClasses->getModule());
    }

    public function testGetControllerAction()
    {
        $this->requestMock->expects($this->once())
            ->method('getModule')
            ->willReturn('foo');
        $this->requestMock->expects($this->once())
            ->method('getController')
            ->willReturn('bar');
        $this->requestMock->expects($this->once())
            ->method('getAction')
            ->willReturn('baz');

        $this->assertEquals('foo-bar-baz', $this->pageCssClasses->getControllerAction());
    }

    public function testGetDetails()
    {
        $this->requestMock->expects($this->once())
            ->method('getModule')
            ->willReturn('foo');
        $this->requestMock->expects($this->once())
            ->method('getController')
            ->willReturn('bar');
        $this->titleMock->expects($this->once())
            ->method('getPageTitle')
            ->willReturn('speciäl-chörs_0tßst');
        $this->stringFormatterMock->expects($this->once())
            ->method('makeStringUrlSafe')
            ->with('speciäl-chörs_0tßst')
            ->willReturn('speciael-choers-0tssst');

        $this->assertEquals('foo-bar-speciael-choers-0tssst', $this->pageCssClasses->getDetails());
    }
}
