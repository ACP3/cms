<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use ACP3\Core\Cache;

class CachingMenuService implements MenuServiceInterface
{
    private const CACHE_KEY_ALL_MENU_ITEMS = 'menu_items';
    private const CACHE_KEY_VISIBLE_MENU_ITEMS = 'menu_items_visible_%s';

    /**
     * @var Cache
     */
    private $menusCache;
    /**
     * @var MenuService
     */
    private $menuService;

    public function __construct(Cache $menusCache, MenuService $menuService)
    {
        $this->menusCache = $menusCache;
        $this->menuService = $menuService;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllMenuItems(): array
    {
        if (!$this->menusCache->contains(self::CACHE_KEY_ALL_MENU_ITEMS)) {
            $this->menusCache->save(self::CACHE_KEY_ALL_MENU_ITEMS, $this->menuService->getAllMenuItems());
        }

        return $this->menusCache->fetch(self::CACHE_KEY_ALL_MENU_ITEMS);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getVisibleMenuItemsByMenu(string $menuIdentifier): array
    {
        $cacheKey = sprintf(self::CACHE_KEY_VISIBLE_MENU_ITEMS, $menuIdentifier);

        if (!$this->menusCache->contains($cacheKey)) {
            $this->menusCache->save($cacheKey, $this->menuService->getVisibleMenuItemsByMenu($menuIdentifier));
        }

        return $this->menusCache->fetch($cacheKey);
    }
}
