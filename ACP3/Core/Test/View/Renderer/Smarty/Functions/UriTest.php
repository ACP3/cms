<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

/**
 * Created by PhpStorm.
 * User: tinog
 * Date: 26.03.2017
 * Time: 21:26
 */

namespace ACP3\Core\Test\View\Renderer\Smarty\Functions;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Test\View\Renderer\Smarty\AbstractPluginTest;
use ACP3\Core\View\Renderer\Smarty\Functions\Uri;

class UriTest extends AbstractPluginTest
{
    /**
     * @var Uri
     */
    protected $plugin;
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
        $this->routerMock = $this->getMockBuilder(RouterInterface::class)
            ->setMethods(['route'])
            ->getMock();
        $this->smartyInternalTemplateMock = $this->getMockBuilder(\Smarty_Internal_Template::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return string
     */
    protected function getExpectedExtensionName()
    {
        return 'uri';
    }

    public function testUriWithRouteOnly()
    {
        $params = [
            'args' => 'foo/bar/baz'
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('foo/bar/baz', false, null)
            ->willReturn('/foo/bar/baz/');

        $this->assertEquals('/foo/bar/baz/', $this->plugin->process($params, $this->smartyInternalTemplateMock));
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

        $this->assertEquals('http://example.com/foo/bar/baz/', $this->plugin->process($params, $this->smartyInternalTemplateMock));
    }

    public function testUriWithForceHttps()
    {
        $params = [
            'args' => 'foo/bar/baz',
            'secure' => true
        ];

        $this->routerMock->expects($this->once())
            ->method('route')
            ->with('foo/bar/baz', false, true)
            ->willReturn('https://example.com/foo/bar/baz/');

        $this->assertEquals('https://example.com/foo/bar/baz/', $this->plugin->process($params, $this->smartyInternalTemplateMock));
    }
}
