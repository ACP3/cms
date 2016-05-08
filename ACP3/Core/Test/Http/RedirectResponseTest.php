<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Http;


use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\Request;
use ACP3\Core\Router;
use Symfony\Component\HttpFoundation\JsonResponse;

class RedirectResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
    /**
     * @var RedirectResponse
     */
    private $redirectResponse;

    protected function setUp()
    {
        $this->setUpMockObjects();
        
        $this->redirectResponse = new RedirectResponse(
            $this->requestMock,
            $this->routerMock
        );
    }

    private function setUpMockObjects()
    {
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testRedirectToExternalWebsite()
    {
        $this->setUpRequestMockExpectations(false);

        $response = $this->redirectResponse->toNewPage('http://www.google.de');

        $this->assertInstanceOf(
            \Symfony\Component\HttpFoundation\RedirectResponse::class,
            $response
        );
        $this->assertEquals('http://www.google.de', $response->getTargetUrl());
    }

    /**
     * @param bool $isAjax
     */
    private function setUpRequestMockExpectations($isAjax)
    {
        $this->requestMock->expects($this->once())
            ->method('isAjax')
            ->willReturn($isAjax);
    }

    public function testAjaxRedirectToExternalWebsite()
    {
        $this->setUpRequestMockExpectations(true);

        $response = $this->redirectResponse->toNewPage('http://www.google.de');

        $this->assertInstanceOf(
            JsonResponse::class,
            $response
        );

        $this->assertEquals($this->buildJsonResponseContent('http:\/\/www.google.de'), $response->getContent());
    }

    /**
     * @param string $url
     * @return string
     */
    private function buildJsonResponseContent($url)
    {
        return <<<JSON
{"redirect_url":"$url"}
JSON;
    }

    public function testTemporaryRedirect()
    {
        $this->setUpRequestMockExpectations(false);
        $this->setUpRouterMockExpectations('foo/bar/baz');

        $response = $this->redirectResponse->temporary('foo/bar/baz');

        $this->assertInstanceOf(
            \Symfony\Component\HttpFoundation\RedirectResponse::class,
            $response
        );
        $this->assertEquals('http://www.example.com/foo/bar/baz/', $response->getTargetUrl());
    }

    private function setUpRouterMockExpectations($path)
    {
        $this->routerMock->expects($this->once())
            ->method('route')
            ->with($path, true)
            ->willReturn('http://www.example.com/' . $path . '/');
    }

    public function testAjaxTemporaryRedirect()
    {
        $this->setUpRequestMockExpectations(true);
        $this->setUpRouterMockExpectations('foo/bar/baz');

        $response = $this->redirectResponse->temporary('foo/bar/baz');

        $this->assertInstanceOf(
            JsonResponse::class,
            $response
        );
        $this->assertEquals(
            $this->buildJsonResponseContent('http:\/\/www.example.com\/foo\/bar\/baz\/'),
            $response->getContent()
        );
    }

    public function testPermanentRedirect()
    {
        $this->setUpRequestMockExpectations(false);
        $this->setUpRouterMockExpectations('foo/bar/baz');

        $response = $this->redirectResponse->permanent('foo/bar/baz');

        $this->assertInstanceOf(
            \Symfony\Component\HttpFoundation\RedirectResponse::class,
            $response
        );
        $this->assertEquals('http://www.example.com/foo/bar/baz/', $response->getTargetUrl());
    }

    public function testAjaxPermanentRedirect()
    {
        $this->setUpRequestMockExpectations(true);
        $this->setUpRouterMockExpectations('foo/bar/baz');

        $response = $this->redirectResponse->permanent('foo/bar/baz');

        $this->assertInstanceOf(
            JsonResponse::class,
            $response
        );
        $this->assertEquals(
            $this->buildJsonResponseContent('http:\/\/www.example.com\/foo\/bar\/baz\/'),
            $response->getContent()
        );
    }
}
