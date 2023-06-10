<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Menus\Services\MenuServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MenuItemsListTest extends TestCase
{
    private MenuItemsList $menuItemsList;

    private Forms&MockObject $formsHelper;

    private MenuServiceInterface&MockObject $menuService;

    /**
     * @return array<string, array<mixed>>
     */
    public static function menuItemsListProvider(): array
    {
        $menuItemList = [
            [
                'id' => 1,
                'level' => 0,
                'parent_id' => null,
                'left_id' => 1,
                'right_id' => 2,
                'block_name' => 'foo-block',
                'block_title' => 'Foo-Block',
                'block_id' => 10,
            ],
            [
                'id' => 2,
                'level' => 0,
                'parent_id' => null,
                'left_id' => 3,
                'right_id' => 6,
                'block_name' => 'foo-block',
                'block_title' => 'Foo-Block',
                'block_id' => 10,
            ],
            [
                'id' => 3,
                'level' => 1,
                'parent_id' => 2,
                'left_id' => 4,
                'right_id' => 5,
                'block_name' => 'foo-block',
                'block_title' => 'Foo-Block',
                'block_id' => 10,
            ],
        ];

        return [
            'entry-without-superior-page' => [
                0,
                0,
                0,
                $menuItemList,
                [
                    'foo-block' => [
                        'title' => 'Foo-Block',
                        'menu_id' => 10,
                        'items' => [
                            [
                                'id' => 1,
                                'level' => 0,
                                'parent_id' => null,
                                'left_id' => 1,
                                'right_id' => 2,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '',
                                'selected' => '',
                            ],
                            [
                                'id' => 2,
                                'level' => 0,
                                'parent_id' => null,
                                'left_id' => 3,
                                'right_id' => 6,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '',
                                'selected' => '',
                            ],
                            [
                                'id' => 3,
                                'level' => 1,
                                'parent_id' => 2,
                                'left_id' => 4,
                                'right_id' => 5,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '&nbsp;&nbsp;',
                                'selected' => '',
                            ],
                        ],
                    ],
                ],
            ],
            'entry-with-superior-page' => [
                2,
                4,
                5,
                $menuItemList,
                [
                    'foo-block' => [
                        'title' => 'Foo-Block',
                        'menu_id' => 10,
                        'items' => [
                            [
                                'id' => 1,
                                'level' => 0,
                                'parent_id' => null,
                                'left_id' => 1,
                                'right_id' => 2,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '',
                                'selected' => '',
                            ],
                            [
                                'id' => 2,
                                'level' => 0,
                                'parent_id' => null,
                                'left_id' => 3,
                                'right_id' => 6,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '',
                                'selected' => '',
                            ],
                        ],
                    ],
                ],
            ],
            'entry-with-descendant-page' => [
                0,
                3,
                6,
                $menuItemList,
                [
                    'foo-block' => [
                        'title' => 'Foo-Block',
                        'menu_id' => 10,
                        'items' => [
                            [
                                'id' => 1,
                                'level' => 0,
                                'parent_id' => null,
                                'left_id' => 1,
                                'right_id' => 2,
                                'block_name' => 'foo-block',
                                'block_title' => 'Foo-Block',
                                'block_id' => 10,
                                'spaces' => '',
                                'selected' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->formsHelper = $this->createMock(Forms::class);
        $this->menuService = $this->createMock(MenuServiceInterface::class);

        $this->menuItemsList = new MenuItemsList($this->formsHelper, $this->menuService);
    }

    /**
     * @dataProvider menuItemsListProvider
     *
     * @param array<array<string, mixed>>                                                           $menuItemList
     * @param array<string, array{title: string, menu_id: int, items: array<array<string, mixed>>}> $expected
     */
    public function testMenuItemsList(int $parentId, int $leftId, int $rightId, array $menuItemList, array $expected): void
    {
        $this->formsHelper->method('selectEntry')->willReturn('');

        $this->menuService->expects(self::once())
            ->method('getAllMenuItems')
            ->willReturn($menuItemList);

        self::assertEquals($expected, $this->menuItemsList->menuItemsList($parentId, $leftId, $rightId));
    }
}
