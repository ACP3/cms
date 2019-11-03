<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;

class ManageMenuItem
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var MenuItemsModel
     */
    protected $menuItemsModel;

    public function __construct(
        MenuItemsModel $menuItemsModel,
        MenuItemRepository $menuItemRepository
    ) {
        $this->menuItemRepository = $menuItemRepository;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function manageMenuItem(string $path, array $data = []): bool
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($path);
        $result = true;

        if (empty($data) === false) {
            $data['uri'] = $path;
            $result = $this->menuItemsModel->save($data, $menuItem['id'] ?? null) !== false;
        } elseif (!empty($menuItem)) {
            $result = $this->menuItemsModel->delete($menuItem['id']) > 0;
        }

        return $result;
    }
}
