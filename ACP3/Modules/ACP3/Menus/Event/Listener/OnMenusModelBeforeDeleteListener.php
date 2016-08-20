<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;


use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Modules\ACP3\Menus\Cache;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

class OnMenusModelBeforeDeleteListener
{
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var MenuRepository
     */
    protected $menuRepository;
    /**
     * @var MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var Delete
     */
    protected $deleteOperation;

    /**
     * OnMenusModelBeforeDeleteListener constructor.
     * @param Cache $cache
     * @param Delete $deleteOperation
     * @param MenuRepository $menuRepository
     * @param MenuItemRepository $menuItemRepository
     */
    public function __construct(
        Cache $cache,
        Delete $deleteOperation,
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository
    ) {
        $this->cache = $cache;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->deleteOperation = $deleteOperation;
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
                    $this->deleteOperation->execute($menuItem['id']);
                }

                $menuName = $this->menuRepository->getMenuNameById($item);
                $this->cache->getCacheDriver()->delete(Cache::CACHE_ID_VISIBLE . $menuName);
            }
        }
    }
}
