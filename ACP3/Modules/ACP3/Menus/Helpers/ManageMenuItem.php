<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository;

class ManageMenuItem
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository
     */
    protected $menuItemRepository;
    /**
     * @var MenuItemsModel
     */
    protected $menuItemsModel;

    /**
     * @param MenuItemsModel                                                $menuItemsModel
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository $menuItemRepository
     */
    public function __construct(
        MenuItemsModel $menuItemsModel,
        MenuItemsRepository $menuItemRepository
    ) {
        $this->menuItemRepository = $menuItemRepository;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @param string $menuItemUri
     * @param bool   $createOrUpdateMenuItem
     * @param array  $data
     *
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function manageMenuItem(string $menuItemUri, bool $createOrUpdateMenuItem, array $data = [])
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($menuItemUri);
        $result = true;

        if ($createOrUpdateMenuItem === true) {
            if (empty($menuItem)) {
                $result = $this->createMenuItem($data, $menuItemUri);
            } else {
                $result = $this->updateMenuItem($data, $menuItem);
            }
        } elseif (!empty($menuItem)) {
            $result = $this->menuItemsModel->delete($menuItem['id']) > 0;
        }

        return $result;
    }

    /**
     * @param array  $data
     * @param string $menuItemUri
     *
     * @return bool
     */
    protected function createMenuItem(array $data, string $menuItemUri): bool
    {
        $data['uri'] = $menuItemUri;

        return (bool) $this->menuItemsModel->save($data);
    }

    /**
     * @param array $data
     * @param array $menuItem
     *
     * @return bool
     */
    protected function updateMenuItem(array $data, array $menuItem): bool
    {
        return (bool) $this->menuItemsModel->save($data, $menuItem['id']);
    }
}
