<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Test\Core;


use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Http\Request;
use ACP3\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Core\Router;

class RouterTest extends \ACP3\Core\Test\RouterTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $aliasesMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->router = new Router(
            $this->aliasesMock,
            $this->requestMock,
            $this->appPathMock,
            $this->configMock,
            ApplicationMode::PRODUCTION
        );
    }

    protected function initializeMockObjects()
    {
        $this->aliasesMock = $this->getMockBuilder(Aliases::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUriAlias'])
            ->getMock();

        parent::initializeMockObjects();
    }

    public function testRouteWithNoUriAlias()
    {
        $path = 'news/index/index/';

        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();
        $this->setUpAliasesMockExpectations(1, $path);

        $expected = '/index.php/news/index/index/';

        $this->assertEquals($expected, $this->router->route($path));
    }

    public function testRouteWithExistingUriAlias()
    {
        $this->setUpRequestMockExpectations();
        $this->setAppPathMockExpectations(0, 1);
        $this->setUpConfigMockExpectations();
        $this->setUpAliasesMockExpectations(1, 'lorem-ipsum-dolor');

        $path = 'news/index/index/';
        $expected = '/index.php/lorem-ipsum-dolor/';

        $this->assertEquals($expected, $this->router->route($path));
    }

    /**
     * @param int    $callCount
     * @param string $returnValue
     */
    private function setUpAliasesMockExpectations($callCount = 0, $returnValue)
    {
        $this->aliasesMock->expects($this->exactly($callCount))
            ->method('getUriAlias')
            ->willReturn($returnValue);
    }

    public function testRouteUseNoModRewrite()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testRouteUseNoModRewrite();
    }

    public function testRouteUseModRewrite()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testRouteUseModRewrite();
    }

    public function testRouteAddTrailingSlash()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testRouteAddTrailingSlash();
    }

    public function testRouteWithAdminUrl()
    {
        $this->setUpAliasesMockExpectations(0, 'acp/index/index/');

        parent::testRouteWithAdminUrl();
    }

    public function testRouteWithAclResourcePath()
    {
        $this->setUpAliasesMockExpectations(0, 'admin/news/index/index/');

        parent::testRouteWithAclResourcePath();
    }

    public function testRouteWithAdminUrlModRewriteEnabled()
    {
        $this->setUpAliasesMockExpectations(0, 'acp/news/index/index/');

        parent::testRouteWithAdminUrlModRewriteEnabled();
    }

    public function testAbsoluteRoute()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testAbsoluteRoute();
    }

    public function testSecureRoute()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testSecureRoute();
    }

    public function testRouteAppendControllerAndControllerAction()
    {
        $this->setUpAliasesMockExpectations(1, 'news/index/index/');

        parent::testRouteAppendControllerAndControllerAction();
    }

    public function testRouteCompleteDefaultAdminPanelUrl()
    {
        $this->setUpAliasesMockExpectations(0, 'acp/acp/index/index/');

        parent::testRouteCompleteDefaultAdminPanelUrl();
    }
}
