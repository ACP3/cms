<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Http;

use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Router\RouterInterface;

class RedirectResponseTest extends \PHPUnit_Framework_TestCase
{
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
            $this->routerMock
        );
    }

    private function setUpMockObjects()
    {
        $this->routerMock = $this->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['route'])
            ->getMock();
    }

    public function testRedirectToExternalWebsite()
    {
        $response = $this->redirectResponse->toNewPage('http://www.google.de');

        $this->assertInstanceOf(
            \Symfony\Component\HttpFoundation\RedirectResponse::class,
            $response
        );
        $this->assertEquals('http://www.google.de', $response->getTargetUrl());
    }

    public function testTemporaryRedirect()
    {
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

    public function testPermanentRedirect()
    {
        $this->setUpRouterMockExpectations('foo/bar/baz');

        $response = $this->redirectResponse->permanent('foo/bar/baz');

        $this->assertInstanceOf(
            \Symfony\Component\HttpFoundation\RedirectResponse::class,
            $response
        );
        $this->assertEquals('http://www.example.com/foo/bar/baz/', $response->getTargetUrl());
    }
}
