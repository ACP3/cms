<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Index
 */
class Delete extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext        $context
     * @param \ACP3\Core\NestedSet                              $nestedSet
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository     $menuRepository
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     * @param \ACP3\Modules\ACP3\Menus\Cache                    $menusCache
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\NestedSet $nestedSet,
        Menus\Model\MenuRepository $menuRepository,
        Menus\Model\MenuItemRepository $menuItemRepository,
        Menus\Cache $menusCache
    ) {
        parent::__construct($context);

        $this->nestedSet = $nestedSet;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
    }

    /**
     * @param string $action
     *
     * @return mixed
     */
    public function actionDelete($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                foreach ($items as $item) {
                    if (!empty($item) && $this->menuRepository->menuExists($item) === true) {
                        // Delete the assigned menu items and update the nested set tree
                        $items = $this->menuItemRepository->getAllItemsByBlockId($item);
                        foreach ($items as $row) {
                            $this->nestedSet->deleteNode(
                                $row['id'],
                                Menus\Model\MenuItemRepository::TABLE_NAME,
                                true
                            );
                        }

                        $block = $this->menuRepository->getMenuNameById($item);
                        $bool = $this->menuRepository->delete($item);
                        $this->menusCache->getCacheDriver()->delete(Menus\Cache::CACHE_ID_VISIBLE . $block);
                    }
                }

                $this->menusCache->saveMenusCache();

                return $bool;
            }
        );
    }
}
