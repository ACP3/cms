<?php
namespace ACP3\Modules\ACP3\Menus;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Menus
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'items';
    const CACHE_ID_VISIBLE = 'visible_items_';

    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository
     */
    protected $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * @param Core\Cache                                        $cache
     * @param \ACP3\Core\I18n\Translator                        $translator
     * @param MenuRepository                                    $menuRepository
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     */
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
     * Returns the cached menu items
     *
     * @return array
     */
    public function getMenusCache()
    {
        if ($this->cache->contains(self::CACHE_ID) === false) {
            $this->saveMenusCache();
        }

        return $this->cache->fetch(self::CACHE_ID);
    }

    /**
     * Saves the menu items to the cache
     *
     * @return boolean
     */
    public function saveMenusCache()
    {
        $menuItems = $this->menuItemRepository->getAllMenuItems();
        $cMenuItems = count($menuItems);

        if ($cMenuItems > 0) {
            $menus = $this->menuRepository->getAllMenus();
            $cMenus = count($menus);

            for ($i = 0; $i < $cMenus; ++$i) {
                $this->saveVisibleMenuItemsCache($menus[$i]['index_name']);
            }

            for ($i = 0; $i < $cMenuItems; ++$i) {
                for ($j = 0; $j < $cMenus; ++$j) {
                    if ($menuItems[$i]['block_id'] == $menus[$j]['id']) {
                        $menuItems[$i]['block_title'] = $menus[$j]['title'];
                        $menuItems[$i]['block_name'] = $menus[$j]['index_name'];
                    }
                }
            }

            $modeSearch = ['1', '2', '3', '4'];
            $modeReplace = [
                $this->translator->t('menus', 'module'),
                $this->translator->t('menus', 'dynamic_page'),
                $this->translator->t('menus', 'hyperlink'),
                $this->translator->t('menus', 'article')
            ];

            for ($i = 0; $i < $cMenuItems; ++$i) {
                $menuItems[$i]['mode_formatted'] = str_replace($modeSearch, $modeReplace, $menuItems[$i]['mode']);
                $menuItems[$i]['first'] = $this->isFirstItemInSet($i, $menuItems);
                $menuItems[$i]['last'] = $this->isLastItemInSet($i, $menuItems);
            }
        }
        return $this->cache->save(self::CACHE_ID, $menuItems);
    }

    /**
     * Saves the visible menu items to the cache
     *
     * @param string $menuIdentifier
     *
     * @return boolean
     */
    public function saveVisibleMenuItemsCache($menuIdentifier)
    {
        return $this->cache->save(
            self::CACHE_ID_VISIBLE . $menuIdentifier,
            $this->menuItemRepository->getVisibleMenuItemsByBlockName($menuIdentifier)
        );
    }

    /**
     * Returns the cached visible menu items
     *
     * @param string $menuIdentifier
     *
     * @return array
     */
    public function getVisibleMenuItems($menuIdentifier)
    {
        if ($this->cache->contains(self::CACHE_ID_VISIBLE . $menuIdentifier) === false) {
            $this->saveVisibleMenuItemsCache($menuIdentifier);
        }

        return $this->cache->fetch(self::CACHE_ID_VISIBLE . $menuIdentifier);
    }

    /**
     * @param int   $index
     * @param array $menuItems
     *
     * @return bool
     */
    protected function isFirstItemInSet($index, array $menuItems)
    {
        if ($index > 0) {
            for ($j = $index - 1; $j >= 0; --$j) {
                if ($menuItems[$j]['parent_id'] == $menuItems[$index]['parent_id']
                    && $menuItems[$j]['block_name'] == $menuItems[$index]['block_name']
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param int   $index
     * @param array $menuItems
     *
     * @return bool
     */
    protected function isLastItemInSet($index, array $menuItems)
    {
        $cItems = count($menuItems);
        for ($j = $index + 1; $j < $cItems; ++$j) {
            if ($menuItems[$index]['parent_id'] == $menuItems[$j]['parent_id']
                && $menuItems[$j]['block_name'] == $menuItems[$index]['block_name']
            ) {
                return false;
            }
        }

        return true;
    }
}
