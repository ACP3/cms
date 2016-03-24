<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test;

use ACP3\Core\Config;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\Request;
use ACP3\Core\Router;

/**
 * Class RouterTest
 * @package ACP3\Core\Test
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $appPathMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->router = new Router(
            $this->requestMock,
            $this->appPathMock,
            $this->configMock,
            ApplicationMode::PRODUCTION
        );
    }

    protected function initializeMockObjects()
    {
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProtocol', 'getHostname'])
            ->getMock();
        $this->appPathMock = $this->getMockBuilder(ApplicationPath::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebRoot', 'getPhpSelf'])
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSettings'])
            ->getMock();
    }

    public function testRouteUseNoModRewrite()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = '/index.php/' . $path;

        $this->assertEquals($expected, $this->router->route($path));
    }

    protected function setUpRequestMockExpectations()
    {
        $this->requestMock->expects($this->any())
            ->method('getProtocol')
            ->willReturn('http://');
        $this->requestMock->expects($this->any())
            ->method('getHostname')
            ->willReturn('example.com');
    }

    /**
     * @param int $callCountWebRoot
     * @param int $callCountPhpSelf
     */
    protected function setAppPathMockExpectations($callCountWebRoot, $callCountPhpSelf)
    {
        $this->appPathMock->expects($this->exactly($callCountWebRoot))
            ->method('getWebRoot')
            ->willReturn('/');
        $this->appPathMock->expects($this->exactly($callCountPhpSelf))
            ->method('getPhpSelf')
            ->willReturn('/index.php');
    }

    /**
     * @param int  $callCount
     * @param bool $useModRewrite
     */
    protected function setUpConfigMockExpectations($callCount = 1, $useModRewrite = false)
    {
        $this->configMock->expects($this->exactly($callCount))
            ->method('getSettings')
            ->with('seo')
            ->willReturn(['mod_rewrite' => $useModRewrite]);
    }

    public function testRouteUseModRewrite()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(1, 0);
        $this->setUpConfigMockExpectations(1, true);

        $path = 'news/index/index/';
        $expected = '/' . $path;

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteAddTrailingSlash()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index';
        $expected = '/index.php/' . $path . '/';

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAdminUrl()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'acp/news/index/index/';
        $expected = '/index.php/' . $path;

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAclResourcePath()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'admin/news/index/index/';
        $expected = '/index.php/acp/news/index/index/';

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAdminUrlModRewriteEnabled()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations(1, true);

        $path = 'acp/news/index/index/';
        $expected = '/index.php/' . $path;

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testAbsoluteRoute()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = 'http://example.com/index.php/' . $path;

        $this->assertEquals($expected, $this->router->route($path, true));
    }

    public function testSecureRoute()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = 'https://example.com/index.php/' . $path;

        $this->assertEquals($expected, $this->router->route($path, false, true));
    }

    public function testRouteAppendControllerAndControllerAction()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news';
        $expected = '/index.php/news/index/index/';

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteCompleteDefaultAdminPanelUrl()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'acp';
        $expected = '/index.php/acp/acp/index/index/';

        $this->assertEquals($expected, $this->router->route($path));
    }
}
