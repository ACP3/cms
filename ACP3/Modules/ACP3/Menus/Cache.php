<?php
namespace ACP3\Modules\ACP3\Menus;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Model\MenuRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Menus
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID = 'items';
    const CACHE_ID_VISIBLE = 'visible_items_';

    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * @param Core\Cache                                        $cache
     * @param Core\Lang                                         $lang
     * @param MenuRepository                                             $menuModel
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     */
    public function __construct(
        Core\Cache $cache,
        Core\Lang $lang,
        MenuRepository $menuModel,
        MenuItemRepository $menuItemRepository
    )
    {
        parent::__construct($cache);

        $this->lang = $lang;
        $this->menuModel = $menuModel;
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
        $items = $this->menuItemRepository->getAllMenuItems();
        $c_items = count($items);

        if ($c_items > 0) {
            $menus = $this->menuModel->getAllMenus();
            $c_menus = count($menus);

            for ($i = 0; $i < $c_menus; ++$i) {
                $this->saveVisibleMenuItemsCache($menus[$i]['index_name']);
            }

            for ($i = 0; $i < $c_items; ++$i) {
                for ($j = 0; $j < $c_menus; ++$j) {
                    if ($items[$i]['block_id'] == $menus[$j]['id']) {
                        $items[$i]['block_title'] = $menus[$j]['title'];
                        $items[$i]['block_name'] = $menus[$j]['index_name'];
                    }
                }
            }

            $modeSearch = ['1', '2', '3', '4'];
            $modeReplace = [
                $this->lang->t('menus', 'module'),
                $this->lang->t('menus', 'dynamic_page'),
                $this->lang->t('menus', 'hyperlink'),
                $this->lang->t('menus', 'article')
            ];

            for ($i = 0; $i < $c_items; ++$i) {
                $items[$i]['mode_formatted'] = str_replace($modeSearch, $modeReplace, $items[$i]['mode']);
                $items[$i]['first'] = $this->isFirstItemInSet($i, $items);
                $items[$i]['last'] = $this->isLastItemInSet($i, $items);
            }
        }
        return $this->cache->save(self::CACHE_ID, $items);
    }

    /**
     * Svaes the visible menu items to the cache
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
     * @param int $index
     * @param array $items
     *
     * @return bool
     */
    protected function isFirstItemInSet($index, array $items)
    {
        if ($index > 0) {
            for ($j = $index - 1; $j >= 0; --$j) {
                if ($items[$j]['parent_id'] == $items[$index]['parent_id'] && $items[$j]['block_name'] == $items[$index]['block_name']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param int $index
     * @param array $items
     *
     * @return bool
     */
    protected function isLastItemInSet($index, array $items)
    {
        $c_items = count($items);
        for ($j = $index + 1; $j < $c_items; ++$j) {
            if ($items[$index]['parent_id'] == $items[$j]['parent_id'] && $items[$j]['block_name'] == $items[$index]['block_name']) {
                return false;
            }
        }

        return true;
    }
}
