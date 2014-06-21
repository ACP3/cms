<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Menus;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'menus';
    const TABLE_NAME_ITEMS = 'menu_items';

    public function menuExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function menuExistsByName($indexName, $id = 0)
    {
        $where = !empty($id) ? ' AND id != :id' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE index_name = :indexName' . $where, array('indexName' => $indexName, 'id' => $id)) > 0 ? true : false;
    }

    public function menuItemExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getOneMenuItemById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function getAllItemsByBlockId($blockId)
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE block_id = ?', array($blockId));
    }

    public function getMenuNameById($id)
    {
        return $this->db->fetchColumn('SELECT index_name FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getMenuItemUriById($id)
    {
        return $this->db->fetchColumn('SELECT uri FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function getMenuItemBlockIdById($id)
    {
        return $this->db->fetchColumn('SELECT block_id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function getMenuItemIdByUri($uri)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE uri = ?', array($uri));
    }

    public function getAllMenus($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY title ASC, id ASC' . $limitStmt);
    }


    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @return boolean
     */
    public function setMenuItemsCache()
    {
        $items = $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
        $c_items = count($items);

        if ($c_items > 0) {
            $menus = $this->getAllMenus();
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

            $mode_search = array('1', '2', '3', '4');
            $mode_replace = array(
                Core\Registry::get('Lang')->t('menus', 'module'),
                Core\Registry::get('Lang')->t('menus', 'dynamic_page'),
                Core\Registry::get('Lang')->t('menus', 'hyperlink'),
                Core\Registry::get('Lang')->t('menus', 'article')
            );

            for ($i = 0; $i < $c_items; ++$i) {
                $items[$i]['mode_formatted'] = str_replace($mode_search, $mode_replace, $items[$i]['mode']);

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
        return Core\Cache::create('items', $items, 'menus');
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @return array
     */
    public function getMenuItemsCache()
    {
        if (Core\Cache::check('items', 'menus') === false) {
            $this->setMenuItemsCache();
        }

        return Core\Cache::output('items', 'menus');
    }

    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @param $block
     * @return boolean
     */
    public function setVisibleMenuItemsCache($block)
    {
        $items = $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n JOIN ' . $this->prefix . static::TABLE_NAME . ' AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id', array($block));
        return Core\Cache::create('visible_items_' . $block, $items, 'menus');
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @param $block
     * @return array
     */
    public function getVisibleMenuItems($block)
    {
        if (Core\Cache::check('visible_items_' . $block, 'menus') === false) {
            $this->setVisibleMenuItemsCache($block);
        }

        return Core\Cache::output('visible_items_' . $block, 'menus');
    }


}
