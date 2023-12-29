<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Router;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;

class RouterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&RequestInterface
     */
    protected $requestMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&ApplicationPath
     */
    protected $appPathMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&SettingsInterface
     */
    protected $configMock;

    protected function setup(): void
    {
        $this->initializeMockObjects();

        $this->router = new Router(
            $this->requestMock,
            $this->appPathMock,
            $this->configMock
        );
    }

    protected function initializeMockObjects(): void
    {
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->appPathMock = $this->createMock(ApplicationPath::class);
        $this->configMock = $this->createMock(SettingsInterface::class);
    }

    public function testRouteUseNoModRewrite(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = '/index.php/' . $path;

        self::assertEquals($expected, $this->router->route($path));
    }

    protected function setUpRequestMockExpectations(): void
    {
        $this->requestMock
            ->method('getScheme')
            ->willReturn('http');
        $this->requestMock
            ->method('getHost')
            ->willReturn('example.com');
    }

    protected function setAppPathMockExpectations(int $callCountWebRoot, int $callCountPhpSelf): void
    {
        $this->appPathMock->expects(self::exactly($callCountWebRoot))
            ->method('getWebRoot')
            ->willReturn('/');
        $this->appPathMock->expects(self::exactly($callCountPhpSelf))
            ->method('getPhpSelf')
            ->willReturn('/index.php');
    }

    protected function setUpConfigMockExpectations(bool $useModRewrite = false): void
    {
        $this->configMock->expects(self::atLeastOnce())
            ->method('getSettings')
            ->with('system')
            ->willReturn(['mod_rewrite' => $useModRewrite, 'homepage' => 'foo/bar/baz/']);
    }

    public function testRouteUseModRewrite(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(1, 0);
        $this->setUpConfigMockExpectations(true);

        $path = 'news/index/index/';
        $expected = '/' . $path;

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testRouteAddTrailingSlash(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index';
        $expected = '/index.php/' . $path . '/';

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAdminUrl(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'acp/news/index/index/';
        $expected = '/index.php/' . $path;

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAclResourcePath(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'admin/news/index/index/';
        $expected = '/index.php/acp/news/index/index/';

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithAdminUrlModRewriteEnabled(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations(true);

        $path = 'acp/news/index/index/';
        $expected = '/index.php/' . $path;

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testAbsoluteRoute(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = 'http://example.com/index.php/' . $path;

        self::assertEquals($expected, $this->router->route($path, true));
    }

    public function testSecureRoute(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news/index/index/';
        $expected = 'https://example.com/index.php/' . $path;

        self::assertEquals($expected, $this->router->route($path, false, true));
    }

    public function testRouteAppendControllerAndControllerAction(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'news';
        $expected = '/index.php/news/index/index/';

        self::assertEquals($expected, $this->router->route($path));
    }

    public function testRouteCompleteDefaultAdminPanelUrl(): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();

        $path = 'acp';
        $expected = '/index.php/acp/acp/index/index/';

        self::assertEquals($expected, $this->router->route($path));
    }

    /**
     * @dataProvider homepageRouteDataProvider()
     */
    public function testRouteIsHomepage(string $path, bool $absolute, ?bool $isSecure, string $expected): void
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(1, 0);
        $this->setUpConfigMockExpectations();

        self::assertEquals($expected, $this->router->route($path, $absolute, $isSecure));
    }

    /**
     * @return mixed[]
     */
    public static function homepageRouteDataProvider(): array
    {
        return [
            ['foo/bar/baz', false, null, '/'],
            ['foo/bar/baz', true, null, 'http://example.com/'],
            ['foo/bar/baz', true, false, 'http://example.com/'],
            ['foo/bar/baz', true, true, 'https://example.com/'],
        ];
    }
}
