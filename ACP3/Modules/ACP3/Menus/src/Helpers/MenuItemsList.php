<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Menus\Services\MenuServiceInterface;

class MenuItemsList
{
    /**
     * @var array<array<string, mixed>>|null
     */
    private ?array $menuItems = null;

    public function __construct(private readonly Forms $formsHelper, private readonly MenuServiceInterface $menuService)
    {
    }

    /**
     * List all available menu items.
     *
     * @return array<string, array{title: string, menu_id: int, items: array<array<string, mixed>>}>
     */
    public function menuItemsList(int $parentId = 0, int $leftId = 0, int $rightId = 0): array
    {
        if ($this->menuItems === null) {
            $this->menuItems = $this->menuService->getAllMenuItems();
        }

        $output = [];

        foreach ($this->menuItems as $row) {
            // A menu item shouldn't be allowed to be a descendant of its own descendants, therefore we filter them out.
            if ($row['left_id'] >= $leftId && $row['right_id'] <= $rightId) {
                continue;
            }

            $row['selected'] = $this->formsHelper->selectEntry('parent_id', $row['id'], $parentId);
            $row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

            $output[$row['block_name']]['title'] = $row['block_title'];
            $output[$row['block_name']]['menu_id'] = $row['block_id'];
            $output[$row['block_name']]['items'][] = $row;
        }

        return $output;
    }
}
