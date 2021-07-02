<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use Psr\Cache\CacheItemPoolInterface;

class CachingMenuService implements MenuServiceInterface
{
    private const CACHE_KEY_ALL_MENU_ITEMS = 'menu_items';
    private const CACHE_KEY_VISIBLE_MENU_ITEMS = 'menu_items_visible_%s';

    /**
     * @var CacheItemPoolInterface
     */
    private $menusCachePool;
    /**
     * @var MenuService
     */
    private $menuService;

    public function __construct(CacheItemPoolInterface $menusCachePool, MenuService $menuService)
    {
        $this->menusCachePool = $menusCachePool;
        $this->menuService = $menuService;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenuItems(): array
    {
        $menuItems = $this->menusCachePool->getItem(self::CACHE_KEY_ALL_MENU_ITEMS);

        if (!$menuItems->isHit()) {
            $menuItems->set($this->menuService->getAllMenuItems());
            $this->menusCachePool->saveDeferred($menuItems);
        }

        return $menuItems->get();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getVisibleMenuItemsByMenu(string $menuIdentifier): array
    {
        $cacheKey = sprintf(self::CACHE_KEY_VISIBLE_MENU_ITEMS, $menuIdentifier);
        $visibleMenuItems = $this->menusCachePool->getItem($cacheKey);

        if (!$visibleMenuItems->isHit()) {
            $visibleMenuItems->set($this->menuService->getVisibleMenuItemsByMenu($menuIdentifier));
            $this->menusCachePool->saveDeferred($visibleMenuItems);
        }

        return $visibleMenuItems->get();
    }
}
