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

    public function getAllMenuitems()
    {
        return $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
    }

    public function getVisibleMenuItemsByBlockName($blockName)
    {
        return $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n JOIN ' . $this->prefix . static::TABLE_NAME . ' AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id', array($blockName));
    }

    public function getLeftIdByUris($menu, $uris)
    {
        return $this->db->executeQuery('SELECT m.left_id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS m JOIN ' . $this->prefix . static::TABLE_NAME . ' AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC', array($menu, $uris), array(\PDO::PARAM_STR, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY))->fetch(\PDO::FETCH_COLUMN);
    }
}
