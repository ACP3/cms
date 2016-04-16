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
    protected $containerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $routerMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $steps;

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
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations();

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/acp/foo',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    /**
     * @param string $area
     * @param string $moduleName
     * @param string $controller
     * @param string $action
     */
    private function setUpRequestMockExpectations($area, $moduleName, $controller, $action)
    {
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getArea')
            ->willReturn($area);
        $this->requestMock->expects($this->any())
            ->method('getModule')
            ->willReturn($moduleName);
        $this->requestMock->expects($this->any())
            ->method('getController')
            ->willReturn($controller);
        $this->requestMock->expects($this->any())
            ->method('getAction')
            ->willReturn($action);
        $this->requestMock->expects($this->any())
            ->method('getModuleAndController')
            ->willReturn(
                ($area === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $moduleName . '/' . $controller
            );
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

    private function setUpRouterMockExpectations()
    {
        $this->routerMock->expects($this->atLeastOnce())
            ->method('route')
            ->willReturnCallback(function($path) {
                return '/' . $path;
            });
    }

    private function setUpTranslatorMockExpectations($callCount = 1)
    {
        $this->translatorMock->expects($this->atLeast($callCount))
            ->method('t')
            ->willReturnCallback(function($module, $phrase) {
                return strtoupper ('{' . $module . '_' . $phrase . '}');
            });
    }

    public function testGetBreadcrumbForAdmin()
    {
        $this->setUpContainerMockExpectations(
            'foo.controller.admin.details.index',
            true
        );
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_ADMIN,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations();

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/acp/foo',
            ],
            [
                'title' => '{FOO_ADMIN_DETAILS_INDEX}',
                'uri' => '/acp/foo/details',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForAdminWithExistingSteps()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_ADMIN,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations();

        $this->steps->append('FooBarBaz', 'acp/foo/bar/baz');

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/acp/foo',
            ],
            [
                'title' => 'FooBarBaz',
                'uri' => '/acp/foo/bar/baz',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendControllerIndex()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'foo',
            'index',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations();

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/foo',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendController()
    {
        $this->setUpContainerMockExpectations(
            'foo.controller.frontend.details.index',
            true
        );
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations();

        $expected = [
            [
                'title' => '{FOO_FOO}',
                'uri' => '/foo',
            ],
            [
                'title' => '{FOO_FRONTEND_DETAILS_INDEX}',
                'uri' => '/foo/details',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendWithExistingSteps()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('FooBarBaz', 'foo/bar/baz');

        $expected = [
            [
                'title' => 'FooBarBaz',
                'uri' => '/foo/bar/baz',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testAddMultipleSameSteps()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('FooBarBaz', 'foo/bar/baz');
        $this->steps->append('FooBarBaz', 'foo/bar/baz');
        $this->steps->append('FooBarBaz', 'foo/bar/baz');

        $expected = [
            [
                'title' => 'FooBarBaz',
                'uri' => '/foo/bar/baz',
                'last' => true
            ]
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testReplaceAncestor()
    {
        $this->setUpRequestMockExpectations(
            AreaEnum::AREA_FRONTEND,
            'foo',
            'details',
            'index'
        );
        $this->setUpRouterMockExpectations();
        $this->setUpTranslatorMockExpectations(0);

        $this->steps->append('FooBarBaz', 'foo/bar/baz');
        $this->steps->append('FooBarBaz2', 'foo/bar/baz2');
        $this->steps->append('FooBarBaz3', 'foo/bar/baz3');

        $this->steps->replaceAncestor('Lorem Ipsum', 'lorem/ipsum/dolor');

        $expected = [
            [
                'title' => 'FooBarBaz',
                'uri' => '/foo/bar/baz',
            ],
            [
                'title' => 'FooBarBaz2',
                'uri' => '/foo/bar/baz2',
            ],
            [
                'title' => 'Lorem Ipsum',
                'uri' => '/lorem/ipsum/dolor',
                'last' => true
            ],
        ];
        $this->assertEquals($expected, $this->steps->getBreadcrumb());
    }
}
