<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;

class MenuService implements MenuServiceInterface
{
    public function __construct(private readonly MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenuItems(): array
    {
        return $this->menuItemRepository->getAllMenuItems();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getVisibleMenuItemsByMenu(string $menuIdentifier): array
    {
        return $this->menuItemRepository->getVisibleMenuItemsByBlockName($menuIdentifier);
    }
}
