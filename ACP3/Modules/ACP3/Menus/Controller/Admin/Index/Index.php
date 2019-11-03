<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository
     */
    protected $menuRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext            $context
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList           $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository $menuRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Menus\Helpers\MenuItemsList $menusHelpers,
        Menus\Model\Repository\MenuRepository $menuRepository
    ) {
        parent::__construct($context);

        $this->menusHelpers = $menusHelpers;
        $this->menuRepository = $menuRepository;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute()
    {
        $menus = $this->menuRepository->getAllMenus();

        if (\count($menus) > 0) {
            $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
            $canEditItem = $this->acl->hasPermission('admin/menus/items/edit');
            $canSortItem = $this->acl->hasPermission('admin/menus/items/order');

            $this->view->assign([
                'pages_list' => $this->fetchMenuItems($menus),
                'can_delete_item' => $canDeleteItem,
                'can_edit_item' => $canEditItem,
                'can_order_item' => $canSortItem,
                'can_delete' => $this->acl->hasPermission('admin/menus/index/delete'),
                'can_edit' => $this->acl->hasPermission('admin/menus/index/edit'),
                'colspan' => $this->getColspan($canDeleteItem, $canEditItem, $canSortItem),
            ]);
        }
    }

    private function fetchMenuItems(array $menus): array
    {
        $menuItems = $this->menusHelpers->menuItemsList();
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
