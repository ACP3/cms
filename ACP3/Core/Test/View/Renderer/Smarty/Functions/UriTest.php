<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test\View\Renderer\Smarty\Functions;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\View\Renderer\Smarty\Functions\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    /**
     * @var Uri
     */
    private $plugin;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $smartyInternalTemplateMock;

    protected function setUp()
    {
        $this->setUpMockObjects();

        $this->plugin = new Uri($this->routerMock);
    }

    private function setUpMockObjects()
    {
        $this->routerMock = $this->createMock(RouterInterface::class);
        $this->smartyInternalTemplateMock = $this->createMock(\Smarty_Internal_Template::class);
    }

    public function testUriWithRouteOnly()
    {
        $params = [
            'args' => 'foo/bar/baz',
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('foo/bar/baz', false, null)
            ->willReturn('/foo/bar/baz/');

        $this->assertEquals('/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }

    public function testUriWithForceHttp()
    {
        $params = [
            'args' => 'foo/bar/baz',
            'secure' => false,
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('foo/bar/baz', false, false)
            ->willReturn('http://example.com/foo/bar/baz/');

        $this->assertEquals('http://example.com/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }

    public function testUriWithForceHttps()
    {
        $params = [
            'args' => 'foo/bar/baz',
            'secure' => true,
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('foo/bar/baz', false, true)
            ->willReturn('https://example.com/foo/bar/baz/');

        $this->assertEquals('https://example.com/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }
}
