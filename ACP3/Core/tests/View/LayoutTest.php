<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\View\Layout;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testGetAvailableLayouts(): void
    {
        $structure = [
            'designs' => [
                'acp3' => [
                    'Foo' => [
                        'Resources' => [
                            'View' => [
                                'layout.tpl' => '',
                                'Frontend' => [
                                    'layout.tpl' => '',
                                ],
                            ],
                        ],
                    ],
                    'Bar' => [
                        'Resources' => [
                            'View' => [
                                'Frontend' => [
                                    'layout.baz.tpl' => '',
                                ],
                            ],
                        ],
                    ],
                    'System' => [
                        'Resources' => [
                            'View' => [
                                'layout.tpl' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        vfsStream::setup('root', null, $structure);

        $themePathMock = $this->createMock(ThemePathInterface::class);
        $themePathMock->method('getDesignPathInternal')
            ->willReturn(vfsStream::url('root/designs/acp3'));

        $layout = new Layout($themePathMock);

        self::assertContains('Foo/layout.tpl', $layout->getAvailableLayoutFiles());
        self::assertContains('Foo/Frontend/layout.tpl', $layout->getAvailableLayoutFiles());
        self::assertContains('Bar/Frontend/layout.baz.tpl', $layout->getAvailableLayoutFiles());
        self::assertNotContains('System/layout.tpl', $layout->getAvailableLayoutFiles());
    }
}
