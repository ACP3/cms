<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Modules\ACP3\Menus\Repository\MenuRepository;
use ACP3\Modules\ACP3\Menus\ViewProviders\MenuItemsDataGridViewProvider;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends AbstractWidgetAction implements InvokableActionInterface
{
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Repository\MenuRepository
     */
    private $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\ViewProviders\MenuItemsDataGridViewProvider
     */
    private $menuItemsDataGridViewProvider;

    public function __construct(
        WidgetContext $context,
        ACL $acl,
        MenuRepository $menuRepository,
        MenuItemsDataGridViewProvider $menuItemsDataGridViewProvider
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->menuRepository = $menuRepository;
        $this->menuItemsDataGridViewProvider = $menuItemsDataGridViewProvider;
    }

    /**
     * @returns array|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        $menus = $this->menuRepository->getAllMenus();

        $dataGrids = [];
        foreach ($menus as $menu) {
            $dataGrids[$menu['id']] = ($this->menuItemsDataGridViewProvider)($menu['id']);
        }

        foreach ($dataGrids as $dataGrid) {
            if ($dataGrid instanceof JsonResponse) {
                return $dataGrid;
            }
        }

        return [
            'menus' => $menus,
            'data_grids' => $dataGrids,
            'can_delete' => $this->acl->hasPermission('admin/menus/index/delete'),
            'can_edit' => $this->acl->hasPermission('admin/menus/index/edit'),
        ];
    }
}
