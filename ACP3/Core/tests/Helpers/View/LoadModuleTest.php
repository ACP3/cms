<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\ACL;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class LoadModuleTest extends TestCase
{
    /**
     * @dataProvider loadModuleDataProvider
     *
     * @param array{module: string, args: array<string, mixed>} $params
     *
     * @throws Exception
     */
    public function testInvoke(string $expected, string $modulePath, array $params): void
    {
        $aclMock = $this->createMock(ACL::class);
        $fragmentHandlerMock = $this->createMock(FragmentHandler::class);

        $aclMock->method('hasPermission')
            ->willReturn(true);
        $fragmentHandlerMock->method('render')
            ->with($expected, 'esi')
            ->willReturn($expected);

        $loadModule = new LoadModule(
            $aclMock,
            $fragmentHandlerMock,
        );

        self::assertEquals($expected, ($loadModule)($modulePath, $params));
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function loadModuleDataProvider(): array
    {
        return [
            'with-admin-route' => [
                '/acp/foo/index/index',
                'admin/foo/index/index/',
                [],
            ],
            'with-widget-route' => [
                '/widget/foo/bar/index',
                'widget/foo/bar/index/',
                [],
            ],
            'with-frontend-route' => [
                '/foo/bar/baz',
                'frontend/foo/bar/baz/',
                [],
            ],
            'with-incomplete-path' => [
                '/foo/index/index',
                'frontend/foo/',
                [],
            ],
        ];
    }
}
