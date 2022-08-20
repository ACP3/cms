<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MenuItemUpsertServiceTest extends TestCase
{
    private MenuItemUpsertService $menuItemUpsertService;

    private MenuItemsModel&MockObject $menuItemsModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuItemsModel = $this->createMock(MenuItemsModel::class);

        $this->menuItemUpsertService = new MenuItemUpsertService(
            $this->createMock(MenuItemFormValidation::class),
            $this->menuItemsModel
        );
    }

    public function testUpsertWithNewEntry(): void
    {
        $menuItemToBe = [
            'mode' => PageTypeEnum::MODULE->value,
            'module' => 'foo-module',
            'uri' => '',
        ];

        $this->menuItemsModel
            ->expects(self::once())
            ->method('save')
            ->with(['mode' => PageTypeEnum::MODULE->value, 'module' => 'foo-module', 'uri' => 'foo-module'], null);

        $this->menuItemUpsertService->upsert($menuItemToBe);
    }

    public function testUpsertWithExistingEntry(): void
    {
        $menuItem = [
            'mode' => PageTypeEnum::DYNAMIC_PAGE->value,
            'uri' => 'foo/bar/baz/id_1/',
        ];

        $this->menuItemsModel
            ->expects(self::once())
            ->method('save')
            ->with(['mode' => PageTypeEnum::DYNAMIC_PAGE->value, 'uri' => 'foo/bar/baz/id_1/'], 2);

        $this->menuItemUpsertService->upsert($menuItem, 2);
    }
}
