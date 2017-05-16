<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Menus\Cache;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemsRepository;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository;

class OnMenusModelBeforeDeleteListener
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var MenusRepository
     */
    protected $menuRepository;
    /**
     * @var MenuItemsRepository
     */
    protected $menuItemRepository;
    /**
     * @var MenuItemsModel
     */
    protected $menuItemsModel;

    /**
     * OnMenusModelBeforeDeleteListener constructor.
     * @param Cache $cache
     * @param MenusRepository $menuRepository
     * @param MenuItemsRepository $menuItemRepository
     * @param MenuItemsModel $menuItemsModel
     */
    public function __construct(
        Cache $cache,
        MenusRepository $menuRepository,
        MenuItemsRepository $menuItemRepository,
        MenuItemsModel $menuItemsModel
    ) {
        $this->cache = $cache;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            if (!empty($item) && $this->menuRepository->menuExists($item) === true) {
                // Delete the assigned menu items and update the nested set tree
                $menuItems = $this->menuItemRepository->getAllItemsByBlockId($item);
                foreach ($menuItems as $menuItem) {
                    $this->menuItemsModel->delete($menuItem['id']);
                }

                $menuName = $this->menuRepository->getMenuNameById($item);
                $this->cache->getCacheDriver()->delete(Cache::CACHE_ID_VISIBLE . $menuName);
            }
        }
    }
}
