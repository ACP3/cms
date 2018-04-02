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

    public function execute()
    {
        $menus = $this->menuRepository->getAllMenus();
        $cMenus = \count($menus);

        if ($cMenus > 0) {
            $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
            $canEditItem = $this->acl->hasPermission('admin/menus/items/edit');
            $canSortItem = $this->acl->hasPermission('admin/menus/items/order');
            $this->view->assign('can_delete_item', $canDeleteItem);
            $this->view->assign('can_edit_item', $canEditItem);
            $this->view->assign('can_order_item', $canSortItem);
            $this->view->assign('can_delete', $this->acl->hasPermission('admin/menus/index/delete'));
            $this->view->assign('can_edit', $this->acl->hasPermission('admin/menus/index/edit'));

            $colspan = 4;
            if ($canDeleteItem || $canEditItem) {
                ++$colspan;
            }
            if ($canSortItem) {
                ++$colspan;
            }
            $this->view->assign('colspan', $colspan);

            $menuItems = $this->menusHelpers->menuItemsList();
            for ($i = 0; $i < $cMenus; ++$i) {
                if (isset($menuItems[$menus[$i]['index_name']]) === false) {
                    $menuItems[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
                    $menuItems[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
                    $menuItems[$menus[$i]['index_name']]['items'] = [];
                }
            }
            $this->view->assign('pages_list', $menuItems);
        }
    }
}
