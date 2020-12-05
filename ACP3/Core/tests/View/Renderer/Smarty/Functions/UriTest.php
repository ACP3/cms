<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\View\Renderer\Smarty\AbstractPluginTest;

class UriTest extends AbstractPluginTest
{
    /**
     * @var Uri
     */
    protected $plugin;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $routerMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $smartyInternalTemplateMock;

    protected function setup(): void
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

        $this->routerMock->expects(self::once())
            ->method('route')
            ->with('foo/bar/baz', false, null)
            ->willReturn('/foo/bar/baz/');

        self::assertEquals('/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }

    public function testUriWithForceHttp()
    {
        $params = [
            'args' => 'foo/bar/baz',
            'secure' => false,
        ];

        $this->routerMock->expects(self::once())
            ->method('route')
            ->with('foo/bar/baz', false, false)
            ->willReturn('http://example.com/foo/bar/baz/');

        self::assertEquals('http://example.com/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }

    public function testUriWithForceHttps()
    {
        $params = [
            'args' => 'foo/bar/baz',
            'secure' => true,
        ];

        $this->routerMock->expects(self::once())
            ->method('route')
            ->with('foo/bar/baz', false, true)
            ->willReturn('https://example.com/foo/bar/baz/');

        self::assertEquals('https://example.com/foo/bar/baz/', $this->plugin->__invoke($params, $this->smartyInternalTemplateMock));
    }
}
