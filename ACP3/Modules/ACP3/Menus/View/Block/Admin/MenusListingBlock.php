<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository;

class MenusListingBlock extends AbstractBlock
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var MenusRepository
     */
    private $menuRepository;
    /**
     * @var MenuItemsList
     */
    private $menusHelpers;

    /**
     * MenusListingBlock constructor.
     *
     * @param BlockContext    $context
     * @param ACLInterface    $acl
     * @param MenusRepository $menuRepository
     * @param MenuItemsList   $menusHelpers
     */
    public function __construct(
        BlockContext $context,
        ACLInterface $acl,
        MenusRepository $menuRepository,
        MenuItemsList $menusHelpers
    ) {
        parent::__construct($context);
        $this->acl = $acl;
        $this->menuRepository = $menuRepository;
        $this->menusHelpers = $menusHelpers;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $canDeleteItem = $this->acl->hasPermission('admin/menus/items/delete');
        $canEditItem = $this->acl->hasPermission('admin/menus/items/manage');
        $canSortItem = $this->acl->hasPermission('admin/menus/items/order');

        return [
            'can_delete_item' => $canDeleteItem,
            'can_edit_item' => $canEditItem,
            'can_order_item' => $canSortItem,
            'can_delete' => $this->acl->hasPermission('admin/menus/index/delete'),
            'can_edit' => $this->acl->hasPermission('admin/menus/index/manage'),
            'colspan' => $this->calculateColspan($canDeleteItem, $canEditItem, $canSortItem),
            'pages_list' => $this->fetchMenuItems(),
        ];
    }

    /**
     * @return array
     */
    private function fetchMenuItems(): array
    {
        $menus = $this->menuRepository->getAllMenus();
        $cMenus = \count($menus);
        $menuItems = $this->menusHelpers->menuItemsList();
        for ($i = 0; $i < $cMenus; ++$i) {
            if (isset($menuItems[$menus[$i]['index_name']]) === false) {
                $menuItems[$menus[$i]['index_name']]['title'] = $menus[$i]['title'];
                $menuItems[$menus[$i]['index_name']]['menu_id'] = $menus[$i]['id'];
                $menuItems[$menus[$i]['index_name']]['items'] = [];
            }
        }

        return $menuItems;
    }

    /**
     * @param bool $canDeleteItem
     * @param bool $canEditItem
     * @param bool $canSortItem
     *
     * @return int
     */
    private function calculateColspan(bool $canDeleteItem, bool $canEditItem, bool $canSortItem): int
    {
        $colspan = 4;
        if ($canDeleteItem || $canEditItem) {
            $colspan += 1;
        }
        if ($canSortItem) {
            $colspan += 1;
        }

        return $colspan;
    }
}
