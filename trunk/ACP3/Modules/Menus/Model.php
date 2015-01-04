<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Menus
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'menus';
    const TABLE_NAME_ITEMS = 'menu_items';

    /**
     * @param $id
     *
     * @return bool
     */
    public function menuExists($id)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id', ['id' => $id]) > 0);
    }

    /**
     * @param     $indexName
     * @param int $id
     *
     * @return bool
     */
    public function menuExistsByName($indexName, $id = 0)
    {
        $where = !empty($id) ? ' AND id != :id' : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE index_name = :indexName' . $where, ['indexName' => $indexName, 'id' => $id]) > 0);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function menuItemExists($id)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE id = :id', ['id' => $id]) > 0);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneMenuItemById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $uri
     *
     * @return mixed
     */
    public function getOneMenuItemUri($uri)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE uri = ?', [$uri]);
    }

    /**
     * @param $blockId
     *
     * @return array
     */
    public function getAllItemsByBlockId($blockId)
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE block_id = ?', [$blockId]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getMenuNameById($id)
    {
        return $this->db->fetchColumn('SELECT index_name FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getMenuItemUriById($id)
    {
        return $this->db->fetchColumn('SELECT uri FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getMenuItemBlockIdById($id)
    {
        return $this->db->fetchColumn('SELECT block_id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE id = ?', [$id]);
    }

    /**
     * @param $uri
     *
     * @return mixed
     */
    public function getMenuItemIdByUri($uri)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' WHERE uri = ?', [$uri]);
    }

    /**
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllMenus($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY title ASC, id ASC' . $limitStmt);
    }

    /**
     * @return array
     */
    public function getAllMenuItems()
    {
        return $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
    }

    /**
     * @param $blockName
     *
     * @return array
     */
    public function getVisibleMenuItemsByBlockName($blockName)
    {
        return $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS n JOIN ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id', [$blockName]);
    }

    /**
     * @param $menu
     * @param $uris
     *
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLeftIdByUris($menu, $uris)
    {
        return $this->db->executeQuery('SELECT m.left_id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS m JOIN ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC', [$menu, $uris], [\PDO::PARAM_STR, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY])->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * @param $in
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMenuItemsByUri(array $in)
    {
        return $this->db->executeQuery('SELECT p.title, p.uri, p.left_id, p.right_id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS c, ' . $this->db->getPrefix() . static::TABLE_NAME_ITEMS . ' AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) GROUP BY p.uri ORDER BY p.left_id ASC', [$in], [\Doctrine\DBAL\Connection::PARAM_STR_ARRAY])->fetchAll();
    }
}
