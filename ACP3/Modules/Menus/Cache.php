<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;

class Cache
{
    const CACHE_ID = 'items';
    const CACHE_ID_VISIBLE = 'visible_items_';

    /**
     * @var Core\Cache
     */
    protected $cache;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var Model
     */
    protected $menuModel;

    public function __construct(Core\Lang $lang, Model $menuModel)
    {
        $this->cache = new Core\Cache('menus');
        $this->lang = $lang;
        $this->menuModel = $menuModel;
    }

    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @return boolean
     */
    public function setMenuItemsCache()
    {
        $items = $this->menuModel->getAllMenuitems();
        $c_items = count($items);

        if ($c_items > 0) {
            $menus = $this->menuModel->getAllMenus();
            $c_menus = count($menus);

            for ($i = 0; $i < $c_menus; ++$i) {
                $this->setVisibleMenuItemsCache($menus[$i]['index_name']);
            }

            for ($i = 0; $i < $c_items; ++$i) {
                for ($j = 0; $j < $c_menus; ++$j) {
                    if ($items[$i]['block_id'] == $menus[$j]['id']) {
                        $items[$i]['block_title'] = $menus[$j]['title'];
                        $items[$i]['block_name'] = $menus[$j]['index_name'];
                    }
                }
            }

            $modeSearch = array('1', '2', '3', '4');
            $modeReplace = array(
                $this->lang->t('menus', 'module'),
                $this->lang->t('menus', 'dynamic_page'),
                $this->lang->t('menus', 'hyperlink'),
                $this->lang->t('menus', 'article')
            );

            for ($i = 0; $i < $c_items; ++$i) {
                $items[$i]['mode_formatted'] = str_replace($modeSearch, $modeReplace, $items[$i]['mode']);

                // Bestimmen, ob die Seite die Erste und/oder Letzte eines Knotens ist
                $first = $last = true;
                if ($i > 0) {
                    for ($j = $i - 1; $j >= 0; --$j) {
                        if ($items[$j]['parent_id'] == $items[$i]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
                            $first = false;
                            break;
                        }
                    }
                }

                for ($j = $i + 1; $j < $c_items; ++$j) {
                    if ($items[$i]['parent_id'] == $items[$j]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
                        $last = false;
                        break;
                    }
                }

                $items[$i]['first'] = $first;
                $items[$i]['last'] = $last;
            }
        }
        return $this->cache->save(self::CACHE_ID, $items);
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @return array
     */
    public function getMenuItemsCache()
    {
        if ($this->cache->contains(self::CACHE_ID) === false) {
            $this->setMenuItemsCache();
        }

        return $this->cache->fetch(self::CACHE_ID);
    }

    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @param $block
     * @return boolean
     */
    public function setVisibleMenuItemsCache($block)
    {
        $items = $this->menuModel->getVisibleMenuItemsByBlockName($block);
        return $this->cache->save(self::CACHE_ID_VISIBLE . $block, $items);
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @param $block
     * @return array
     */
    public function getVisibleMenuItems($block)
    {
        if ($this->cache->contains(self::CACHE_ID_VISIBLE . $block) === false) {
            $this->setVisibleMenuItemsCache($block);
        }

        return $this->cache->fetch(self::CACHE_ID_VISIBLE . $block);
    }


} 