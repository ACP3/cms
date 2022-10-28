<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class LoadModuleTest extends TestCase
{
    /**
     * @dataProvider loadModuleDataProvider
     *
     * @param array{module: string, args: array<string, mixed>} $params
     */
    public function testInvoke(string $expected, array $params): void
    {
        $smartyInternalTemplateMock = $this->createMock(\Smarty_Internal_Template::class);
        $aclMock = $this->createMock(ACL::class);
        $routerMock = $this->createMock(RouterInterface::class);
        $fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $aclMock->method('hasPermission')
            ->willReturn(true);
        $routerMock->method('route')
            ->with($expected)
            ->willReturn($expected);
        $fragmentHandlerMock->method('render')
            ->with($expected, 'esi')
            ->willReturn($expected);

        $loadModule = new LoadModule(
            $aclMock,
            $routerMock,
            $fragmentHandlerMock
        );

        self::assertEquals($expected, ($loadModule)($params, $smartyInternalTemplateMock));
    }

    /**
     * @return array<string, array{string, array{module: string, args?: array<string, mixed>}}>
     */
    public function loadModuleDataProvider(): array
    {
        return [
            'with-admin-route' => [
                'acp/foo/index/index',
                [
                    'module' => 'admin/foo/index/index/',
                ],
            ],
            'with-widget-route' => [
                'widget/foo/bar/index',
                [
                    'module' => 'widget/foo/bar/index/',
                ],
            ],
            'with-frontend-route' => [
                'foo/bar/baz',
                [
                    'module' => 'frontend/foo/bar/baz/',
                ],
            ],
            'with-incomplete-path' => [
                'foo/index/index',
                [
                    'module' => 'frontend/foo/',
                ],
            ],
        ];
    }
}
