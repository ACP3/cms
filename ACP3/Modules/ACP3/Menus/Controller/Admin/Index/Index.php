<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Index
 */
class Index extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    protected $menusHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext     $context
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList $menusHelpers
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository  $menuRepository
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Menus\Helpers\MenuItemsList $menusHelpers,
        Menus\Model\MenuRepository $menuRepository
    ) {
        parent::__construct($context);

        $this->menusHelpers = $menusHelpers;
        $this->menuRepository = $menuRepository;
    }

    public function execute()
    {
        $menus = $this->menuRepository->getAllMenus();
        $cMenus = count($menus);

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
                $colspan += 1;
            }
            if ($canSortItem) {
                $colspan += 1;
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
