<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Items
 */
class Delete extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var Menus\Model\MenuItemsModel
     */
    protected $menuItemsModel;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param Menus\Model\MenuItemsModel $menuItemsModel
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->menusCache = $menusCache;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @param string $action
     *
     * @return mixed
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $action,
            function (array $items) {
                $result = $this->menuItemsModel->delete($items);

                $this->menusCache->saveMenusCache();

                return $result;
            },
            null,
            'acp/menus'
        );
    }
}
