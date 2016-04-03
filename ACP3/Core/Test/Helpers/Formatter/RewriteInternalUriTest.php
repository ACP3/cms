<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Helpers\Formatter;


use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\Formatter\RewriteInternalUri;
use ACP3\Core\Http\Request;
use ACP3\Core\Modules\Helper\ControllerActionExists;
use ACP3\Modules\ACP3\Seo\Core\Router;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;
use Symfony\Component\HttpFoundation\ServerBag;

class RewriteInternalUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ACP3\Core\Helpers\Formatter\RewriteInternalUri
     */
    private $rewriteInternalUri;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $appPathMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $controllerActionExistsMock;
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
    private $uriAliasValidationRuleMock;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->rewriteInternalUri = new RewriteInternalUri(
            $this->appPathMock,
            $this->controllerActionExistsMock,
            $this->requestMock,
            $this->routerMock,
            $this->uriAliasValidationRuleMock
        );
    }

    private function initializeMockObjects()
    {
        $this->appPathMock = $this->getMockBuilder(ApplicationPath::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->controllerActionExistsMock = $this->getMockBuilder(ControllerActionExists::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->uriAliasValidationRuleMock = $this->getMockBuilder(UriAliasValidationRule::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testRewriteInternalUriWithNotMatchingUrl()
    {
        $this->setUpAppPathExpectations();
        $this->setUpRequestMockExpectations();
        $this->setUpValidationRuleMockExpectations(0, '', false);
        $this->setUpControllerActionExistsMockExpectations(0, '', true);
        $this->setUpRouterMockExpectations(0, '', '');

        $content = <<<HTML
<p>Test</p>
<p><a href="http://google.com">www.google.com</a></p>
HTML;

        $this->assertEquals($content, $this->rewriteInternalUri->rewriteInternalUri($content));
    }

    public function testRewriteInternalUriWithMatchingExternalUri()
    {
        $this->setUpAppPathExpectations();
        $this->setUpRequestMockExpectations();
        $this->setUpValidationRuleMockExpectations(1, 'foo/bar/baz/', false);
        $this->setUpControllerActionExistsMockExpectations(1, 'frontend/foo/bar/baz', false);
        $this->setUpRouterMockExpectations(0, '', '');

        $content = <<<HTML
<p>Test</p>
<p><a href="http://example.com/foo/bar/baz/">www.example.com</a></p>
HTML;

        $this->assertEquals($content, $this->rewriteInternalUri->rewriteInternalUri($content));
    }

    public function testRewriteInternalUriWithMatchingUriAndExistingAlias()
    {
        $this->setUpAppPathExpectations();
        $this->setUpRequestMockExpectations();
        $this->setUpValidationRuleMockExpectations(1, 'foo/bar/baz/', false);
        $this->setUpControllerActionExistsMockExpectations(1, 'frontend/foo/bar/baz', true);
        $this->setUpRouterMockExpectations(1, 'foo/bar/baz/', '/foo-bar/');

        $content = <<<HTML
<p>Test</p>
<p><a href="http://example.com/foo/bar/baz/">www.example.com</a></p>
HTML;

        $expected = <<<HTML
<p>Test</p>
<p><a href="/foo-bar/">www.example.com</a></p>
HTML;

        $this->assertEquals($expected, $this->rewriteInternalUri->rewriteInternalUri($content));
    }

    private function setUpAppPathExpectations()
    {
        $this->appPathMock->expects($this->once())
            ->method('getWebRoot')
            ->willReturn('/');
    }

    /**
     * @param string $httpHost
     */
    private function setUpRequestMockExpectations($httpHost = 'example.com')
    {
        $this->requestMock->expects($this->once())
            ->method('getServer')
            ->willReturn(new ServerBag([
                'HTTP_HOST' => $httpHost
            ]));
    }

    /**
     * @param int    $callCount
     * @param string $uri
     * @param bool   $isValid
     */
    private function setUpValidationRuleMockExpectations($callCount, $uri, $isValid)
    {
        $this->uriAliasValidationRuleMock->expects($this->exactly($callCount))
            ->method('isValid')
            ->with($uri)
            ->willReturn($isValid);
    }

    /**
     * @param int    $callCount
     * @param string $path
     * @param bool   $isControllerAction
     */
    private function setUpControllerActionExistsMockExpectations($callCount, $path, $isControllerAction)
    {
        $this->controllerActionExistsMock->expects($this->exactly($callCount))
            ->method('controllerActionExists')
            ->with($path)
            ->willReturn($isControllerAction);
    }

    /**
     * @param int    $callCount
     * @param string $route
     * @param string $alias
     */
    private function setUpRouterMockExpectations($callCount, $route, $alias)
    {
        $this->routerMock->expects($this->exactly($callCount))
            ->method('route')
            ->with($route)
            ->willReturn($alias);
    }
}
