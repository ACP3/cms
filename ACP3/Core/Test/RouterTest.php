<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Test;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\Request;
use ACP3\Core\Router\Router;
use ACP3\Core\Settings\SettingsInterface;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Router\Router
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
            $this->configMock
        );
    }

    protected function initializeMockObjects()
    {
        $this->requestMock = $this->createMock(Request::class);
        $this->appPathMock = $this->createMock(ApplicationPath::class);
        $this->configMock = $this->createMock(SettingsInterface::class);
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
            ->method('getScheme')
            ->willReturn('http');
        $this->requestMock->expects($this->any())
            ->method('getHost')
            ->willReturn('example.com');
    }

    /**
     * @param int $callCountWebRoot
     * @param int $callCountPhpSelf
     */
    protected function setAppPathMockExpectations(int $callCountWebRoot, int $callCountPhpSelf)
    {
        $this->appPathMock->expects($this->exactly($callCountWebRoot))
            ->method('getWebRoot')
            ->willReturn('/');
        $this->appPathMock->expects($this->exactly($callCountPhpSelf))
            ->method('getPhpSelf')
            ->willReturn('/index.php');
    }

    /**
     * @param bool $useModRewrite
     */
    protected function setUpConfigMockExpectations(bool $useModRewrite = false)
    {
        $this->configMock->expects($this->atLeastOnce())
            ->method('getSettings')
            ->with('system')
            ->willReturn(['mod_rewrite' => $useModRewrite, 'homepage' => 'foo/bar/baz/']);
    }

    public function testRouteUseModRewrite()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(1, 0);
        $this->setUpConfigMockExpectations(true);

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
        $this->setUpConfigMockExpectations(true);

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

    /**
     * @dataProvider homepageRouteDataProvider()
     *
     * @param string    $path
     * @param bool      $absolute
     * @param bool|null $isSecure
     * @param string    $expected
     */
    public function testRouteIsHomepage(string $path, bool $absolute, ?bool $isSecure, string $expected)
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(1, 0);
        $this->setUpConfigMockExpectations();

        $this->assertEquals($expected, $this->router->route($path, $absolute, $isSecure));
    }

    public function homepageRouteDataProvider(): array
    {
        return [
            ['foo/bar/baz', false, null, '/'],
            ['foo/bar/baz', true, null, 'http://example.com/'],
            ['foo/bar/baz', true, false, 'http://example.com/'],
            ['foo/bar/baz', true, true, 'https://example.com/'],
        ];
    }
}
