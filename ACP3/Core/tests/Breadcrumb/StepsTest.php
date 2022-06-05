<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\Request;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StepsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Container
     */
    protected $containerMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Translator
     */
    protected $translatorMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Request
     */
    protected $requestMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|RouterInterface
     */
    protected $routerMock;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcher
     */
    protected $eventDispatcherMock;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $steps;

    protected function setup(): void
    {
        parent::setup();

        $this->initializeMockObjects();

        $this->steps = new Steps(
            $this->containerMock,
            $this->translatorMock,
            $this->requestMock,
            $this->routerMock,
            $this->eventDispatcherMock
        );
    }

    protected function initializeMockObjects(): void
    {
        $this->containerMock = $this->createMock(Container::class);
        $this->translatorMock = $this->createMock(Translator::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->routerMock = $this->createPartialMock(RouterInterface::class, ['route']);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
    }

    public function testGetBreadcrumbForAdminControllerIndex(): void
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
                'uri' => '/acp/foo/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    protected function setUpRequestMockExpectations(AreaEnum $area, string $moduleName, string $controller, string $action, string $parameters = ''): void
    {
        $this->requestMock->expects(self::atLeastOnce())
            ->method('getArea')
            ->willReturn($area);
        $this->requestMock->expects(self::any())
            ->method('getModule')
            ->willReturn($moduleName);
        $this->requestMock->expects(self::any())
            ->method('getController')
            ->willReturn($controller);
        $this->requestMock->expects(self::any())
            ->method('getAction')
            ->willReturn($action);
        $this->requestMock->expects(self::any())
            ->method('getModuleAndController')
            ->willReturn(
                ($area === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $moduleName . '/' . $controller . '/'
            );
        $this->requestMock->expects(self::any())
            ->method('getFullPath')
            ->willReturn(
                ($area === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $moduleName . '/' . $controller . '/' . $action . '/'
            );

        $parameters .= str_ends_with($parameters, '/') ? '' : '/';
        $this->requestMock->expects(self::any())
            ->method('getQuery')
            ->willReturn($moduleName . '/' . $controller . '/' . $action . '/' . $parameters);
        $this->requestMock->expects(self::any())
            ->method('getUriWithoutPages')
            ->willReturn($moduleName . '/' . $controller . '/' . $action . '/' . $parameters);
    }

    private function setUpContainerMockExpectations(string $serviceId, bool $serviceExists): void
    {
        $this->containerMock->expects(self::once())
            ->method('has')
            ->with($serviceId)
            ->willReturn($serviceExists);
    }

    protected function setUpRouterMockExpectations(): void
    {
        $this->routerMock->expects(self::atLeastOnce())
            ->method('route')
            ->willReturnCallback(fn ($path) => '/' . $path . (!preg_match('=/$=', (string) $path) ? '/' : ''));
    }

    protected function setUpTranslatorMockExpectations(int $callCount = 1): void
    {
        $this->translatorMock->expects(self::atLeast($callCount))
            ->method('t')
            ->willReturnCallback(fn ($module, $phrase) => strtoupper('{' . $module . '_' . $phrase . '}'));
    }

    public function testGetBreadcrumbForAdmin(): void
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
                'uri' => '/acp/foo/',
            ],
            [
                'title' => '{FOO_ADMIN_DETAILS_INDEX}',
                'uri' => '/acp/foo/details/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForAdminWithExistingSteps(): void
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
                'uri' => '/acp/foo/',
            ],
            [
                'title' => 'FooBarBaz',
                'uri' => '/acp/foo/bar/baz/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendControllerIndex(): void
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
                'uri' => '/foo/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendController(): void
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
                'uri' => '/foo/',
            ],
            [
                'title' => '{FOO_FRONTEND_DETAILS_INDEX}',
                'uri' => '/foo/details/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testGetBreadcrumbForFrontendWithExistingSteps(): void
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
                'uri' => '/foo/bar/baz/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testAddMultipleSameSteps(): void
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
                'uri' => '/foo/bar/baz/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }

    public function testReplaceAncestor(): void
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
                'uri' => '/foo/bar/baz/',
            ],
            [
                'title' => 'FooBarBaz2',
                'uri' => '/foo/bar/baz2/',
            ],
            [
                'title' => 'Lorem Ipsum',
                'uri' => '/lorem/ipsum/dolor/',
                'last' => true,
            ],
        ];
        self::assertEquals($expected, $this->steps->getBreadcrumb());
    }
}
