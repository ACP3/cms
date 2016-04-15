<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Breadcrumb;


use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StepsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $translatorMock;
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
    private $eventDispatcherMock;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $steps;

    protected function setUp()
    {
        $this->initializeMockObjects();

        $this->steps = new Steps(
            $this->containerMock,
            $this->translatorMock,
            $this->requestMock,
            $this->routerMock,
            $this->eventDispatcherMock
        );
    }

    protected function initializeMockObjects()
    {
        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->translatorMock = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->routerMock = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetBreadcrumbForAdminControllerIndex()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_ADMIN,
            'foo',
            'index',
            'index'
        );
        $this->setUpRouterMockExpectations('acp/foo');
        $this->setUpTranslatorMockExpectations('foo', 'foo');

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/acp/foo',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    private function setUpRequestMockExpectations($area, $moduleName, $controller, $action)
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getArea')
            ->willReturn($area);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getModule')
            ->willReturn($moduleName);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getController')
            ->willReturn($controller);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getAction')
            ->willReturn($action);
    }

    /**
     * @param string $serviceId
     * @param bool $serviceExists
     */
    private function setUpContainerMockExpectations($serviceId, $serviceExists)
    {
        $this->containerMock->expects($this->once())
            ->method('has')
            ->with($serviceId)
            ->willReturn($serviceExists);
    }

    private function setUpRouterMockExpectations($path)
    {
        $this->routerMock->expects($this->atLeastOnce())
            ->method('route')
            ->with($path)
            ->willReturn('/' . $path);
    }

    private function setUpTranslatorMockExpectations($moduleName, $phrase)
    {
        $this->translatorMock->expects($this->atLeastOnce())
            ->method('t')
            ->with($moduleName, $phrase)
            ->willReturn(strtoupper('{' . $moduleName . '_' . $phrase . '}'));
    }
}
