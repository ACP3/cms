<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

class DataGridViewProvider
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    private $menuItemListHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository
     */
    private $menuRepository;

    public function __construct(
        ACL $acl,
        MenuItemsList $menuItemListHelper,
        MenuRepository $menuRepository
    ) {
        $this->acl = $acl;
        $this->menuItemListHelper = $menuItemListHelper;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $menus = $this->menuRepository->getAllMenus();

        $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
        $canEditItem = $this->acl->hasPermission('admin/menus/items/edit');
        $canSortItem = $this->acl->hasPermission('admin/menus/items/order');

        return [
            'pages_list' => \count($menus) > 0 ? $this->fetchMenuItems($menus) : [],
            'can_delete_item' => $canDeleteItem,
            'can_edit_item' => $canEditItem,
            'can_order_item' => $canSortItem,
            'can_delete' => $this->acl->hasPermission('admin/menus/index/delete'),
            'can_edit' => $this->acl->hasPermission('admin/menus/index/edit'),
            'colspan' => $this->getColspan($canDeleteItem, $canEditItem, $canSortItem),
        ];
    }

    private function fetchMenuItems(array $menus): array
    {
        $menuItems = $this->menuItemListHelper->menuItemsList();
        foreach ($menus as $menu) {
            if (isset($menuItems[$menu['index_name']]) === false) {
                $menuItems[$menu['index_name']]['title'] = $menu['title'];
                $menuItems[$menu['index_name']]['menu_id'] = $menu['id'];
                $menuItems[$menu['index_name']]['items'] = [];
            }
        }

        return $menuItems;
    }

    private function getColspan(bool $canDeleteItem, bool $canEditItem, bool $canSortItem): int
    {
        $colspan = 4;
        if ($canDeleteItem || $canEditItem) {
            ++$colspan;
        }
        if ($canSortItem) {
            ++$colspan;
        }

        return $colspan;
    }
}
