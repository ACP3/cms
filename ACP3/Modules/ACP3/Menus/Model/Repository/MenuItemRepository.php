<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model\Repository;

use ACP3\Core\Model\AbstractRepository;

/**
 * Class MenuItemRepository
 * @package ACP3\Modules\ACP3\Menus\Model\Repository
 */
class MenuItemRepository extends AbstractRepository
{
    const TABLE_NAME = 'menu_items';

    /**
     * @param int $menuItemId
     *
     * @return bool
     */
    public function menuItemExists($menuItemId)
    {
        return ((int)$this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id", ['id' => $menuItemId]) > 0);
    }

    /**
     * @param string $uri
     *
     * @return mixed
     */
    public function getOneMenuItemByUri($uri)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE uri = ?",
            [$uri]
        );
    }

    /**
     * @param int $menuId
     *
     * @return array
     */
    public function getAllItemsByBlockId($menuId)
    {
        return $this->db->fetchAll(
            "SELECT `id` FROM {$this->getTableName()} WHERE block_id = ?",
            [$menuId]
        );
    }

    /**
     * @param int $menuItemId
     *
     * @return mixed
     */
    public function getMenuItemUriById($menuItemId)
    {
        return $this->db->fetchColumn(
            "SELECT `uri` FROM {$this->getTableName()} WHERE id = ?",
            [$menuItemId]
        );
    }

    /**
     * @param int $menuItemId
     *
     * @return mixed
     */
    public function getMenuItemMenuIdById($menuItemId)
    {
        return $this->db->fetchColumn(
            "SELECT `block_id` FROM {$this->getTableName()} WHERE id = ?",
            [$menuItemId]
        );
    }

    /**
     * @param string $uri
     *
     * @return mixed
     */
    public function getMenuItemIdByUri($uri)
    {
        return $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE uri = ?",
            [$uri]
        );
    }

    /**
     * @return array
     */
    public function getAllMenuItems()
    {
        return $this->db->fetchAll(
            "SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id"
        );
    }

    /**
     * @param string $blockName
     *
     * @return array
     */
    public function getVisibleMenuItemsByBlockName($blockName)
    {
        return $this->db->fetchAll(
            "SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n JOIN {$this->getTableName(MenuRepository::TABLE_NAME)} AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id",
            [$blockName]
        );
    }

    /**
     * @param string $menuName
     * @param array $uris
     *
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLeftIdByUris($menuName, array $uris)
    {
        return $this->db->executeQuery(
            "SELECT m.left_id FROM {$this->getTableName()} AS m JOIN {$this->getTableName(MenuRepository::TABLE_NAME)} AS b ON(m.block_id = b.id) WHERE b.index_name = ? AND m.uri IN(?) ORDER BY LENGTH(m.uri) DESC",
            [$menuName, $uris],
            [\PDO::PARAM_STR, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY])->fetch(\PDO::FETCH_COLUMN
        );
    }

    /**
     * @param array $in
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMenuItemsByUri(array $in)
    {
        return $this->db->executeQuery(
            "SELECT p.title, p.uri, p.left_id, p.right_id FROM {$this->getTableName()} AS c, {$this->getTableName()} AS p WHERE c.left_id BETWEEN p.left_id AND p.right_id AND c.uri IN(?) GROUP BY p.uri ORDER BY p.left_id ASC",
            [$in],
            [\Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        )->fetchAll();
    }
}
