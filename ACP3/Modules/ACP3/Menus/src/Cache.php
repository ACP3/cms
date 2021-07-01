<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

class Cache extends Core\Modules\AbstractCacheStorage
{
    public const CACHE_ID = 'items';
    public const CACHE_ID_VISIBLE = 'visible_items_';

    /**
     * @var Core\I18n\Translator
     */
    private $translator;
    /**
     * @var MenuRepository
     */
    private $menuRepository;
    /**
     * @var MenuItemRepository
     */
    private $menuItemRepository;

    public function __construct(
        Core\Cache $cache,
        Core\I18n\Translator $translator,
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository
    ) {
        parent::__construct($cache);

        $this->translator = $translator;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * Returns the cached menu items.
     */
    public function getMenusCache(): array
    {
        if ($this->cache->contains(self::CACHE_ID) === false) {
            $this->saveMenusCache();
        }

        return $this->cache->fetch(self::CACHE_ID);
    }

    /**
     * Saves the menu items to the cache.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveMenusCache(): bool
    {
        $menuItems = $this->menuItemRepository->getAllMenuItems();
        $cMenuItems = \count($menuItems);

        if ($cMenuItems > 0) {
            $menus = $this->menuRepository->getAllMenus();

            foreach ($menus as $menu) {
                $this->saveVisibleMenuItemsCache($menu['index_name']);
            }

            foreach ($menuItems as $i => $menuItem) {
                foreach ($menus as $menu) {
                    if ($menuItem['block_id'] === $menu['id']) {
                        $menuItems[$i]['block_title'] = $menu['title'];
                        $menuItems[$i]['block_name'] = $menu['index_name'];
                    }
                }
            }

            $modeSearch = ['1', '2', '3'];
            $modeReplace = [
                $this->translator->t('menus', 'module'),
                $this->translator->t('menus', 'dynamic_page'),
                $this->translator->t('menus', 'hyperlink'),
            ];

            foreach ($menuItems as $i => $menu) {
                $menuItems[$i]['mode_formatted'] = str_replace($modeSearch, $modeReplace, $menu['mode']);
                $menuItems[$i]['first'] = $this->isFirstItemInSet($i, $menuItems);
                $menuItems[$i]['last'] = $this->isLastItemInSet($i, $menuItems);
            }
        }

        return $this->cache->save(self::CACHE_ID, $menuItems);
    }

    /**
     * Saves the visible menu items to the cache.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveVisibleMenuItemsCache(string $menuIdentifier): bool
    {
        return $this->cache->save(
            self::CACHE_ID_VISIBLE . $menuIdentifier,
            $this->menuItemRepository->getVisibleMenuItemsByBlockName($menuIdentifier)
        );
    }

    /**
     * Returns the cached visible menu items.
     */
    public function getVisibleMenuItems(string $menuIdentifier): array
    {
        if ($this->cache->contains(self::CACHE_ID_VISIBLE . $menuIdentifier) === false) {
            $this->saveVisibleMenuItemsCache($menuIdentifier);
        }

        return $this->cache->fetch(self::CACHE_ID_VISIBLE . $menuIdentifier);
    }

    private function isFirstItemInSet(int $index, array $menuItems): bool
    {
        if ($index > 0) {
            for ($j = $index - 1; $j >= 0; --$j) {
                if ($menuItems[$j]['parent_id'] == $menuItems[$index]['parent_id']
                    && $menuItems[$j]['block_name'] === $menuItems[$index]['block_name']
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isLastItemInSet(int $index, array $menuItems): bool
    {
        $cItems = \count($menuItems);
        for ($j = $index + 1; $j < $cItems; ++$j) {
            if ($menuItems[$index]['parent_id'] == $menuItems[$j]['parent_id']
                && $menuItems[$j]['block_name'] === $menuItems[$index]['block_name']
            ) {
                return false;
            }
        }

        return true;
    }
}
