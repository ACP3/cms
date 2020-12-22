<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Cache;

class MenuItemsList
{
    /**
     * @var array
     */
    protected $menuItems = [];
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;

    /**
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        Cache $menusCache
    ) {
        $this->formsHelper = $formsHelper;
        $this->menusCache = $menusCache;
    }

    /**
     * List all available menu items.
     *
     * @param int $parentId
     * @param int $leftId
     * @param int $rightId
     *
     * @return array
     */
    public function menuItemsList($parentId = 0, $leftId = 0, $rightId = 0)
    {
        // Menüpunkte einbinden
        if (empty($this->menuItems)) {
            $this->menuItems = $this->menusCache->getMenusCache();
        }

        $output = [];

        if (\count($this->menuItems) > 0) {
            foreach ($this->menuItems as $row) {
                if (!($row['left_id'] >= $leftId && $row['right_id'] <= $rightId)) {
                    $row['selected'] = $this->formsHelper->selectEntry('parent_id', $row['id'], $parentId);
                    $row['spaces'] = \str_repeat('&nbsp;&nbsp;', $row['level']);

                    // Titel für den aktuellen Block setzen
                    $output[$row['block_name']]['title'] = $row['block_title'];
                    $output[$row['block_name']]['menu_id'] = $row['block_id'];
                    $output[$row['block_name']]['items'][] = $row;
                }
            }
        }

        return $output;
    }
}
