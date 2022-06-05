<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Services\MenuServiceInterface;

class MenuItemsList
{
    /**
     * @var array<array<string, mixed>>|null
     */
    private ?array $menuItems = null;

    public function __construct(private readonly Core\Helpers\Forms $formsHelper, private readonly MenuServiceInterface $menuService)
    {
    }

    /**
     * List all available menu items.
     *
     * @return array<string, array<string, mixed>>
     */
    public function menuItemsList(int $parentId = 0, int $leftId = 0, int $rightId = 0): array
    {
        // Menüpunkte einbinden
        if ($this->menuItems === null) {
            $this->menuItems = $this->menuService->getAllMenuItems();
        }

        $output = [];

        foreach ($this->menuItems as $row) {
            if (!($row['left_id'] >= $leftId && $row['right_id'] <= $rightId)) {
                $row['selected'] = $this->formsHelper->selectEntry('parent_id', $row['id'], $parentId);
                $row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

                // Titel für den aktuellen Block setzen
                $output[$row['block_name']]['title'] = $row['block_title'];
                $output[$row['block_name']]['menu_id'] = $row['block_id'];
                $output[$row['block_name']]['items'][] = $row;
            }
        }

        return $output;
    }
}
