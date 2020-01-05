<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Http\Request;

class PageCssClassesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PageCssClasses
     */
    private $pageCssClasses;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $titleMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
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
        $this->titleMock = $this->createMock(Title::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->stringFormatterMock = $this->createMock(StringFormatter::class);
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
