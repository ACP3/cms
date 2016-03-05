<?php
namespace ACP3\Core\NestedSet;

use ACP3\Core\DB;

/**
 * Class NestedSetRepository
 * @package ACP3\Core\NestedSet
 */
class NestedSetRepository extends \ACP3\Core\Model\AbstractRepository
{
    /**
     * Die aktuelle Seite mit allen untergeordneten Seiten selektieren
     *
     * @param string $tableName
     * @param int    $nodeId
     *
     * @return array
     */
    public function fetchNodeWithSiblings($tableName, $nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$tableName} AS p, {$tableName} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * @param string $tableName
     * @param int    $leftId
     *
     * @return array
     */
    public function fetchNextNodeWithSiblings($tableName, $leftId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$tableName} AS p, {$tableName} AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$leftId]
        );
    }

    /**
     * @param string $tableName
     * @param int    $rightId
     *
     * @return array
     */
    public function fetchPrevNodeWithSiblings($tableName, $rightId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$tableName} AS p, {$tableName} AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$rightId]
        );
    }

    /**
     * @param string $tableName
     * @param int    $nodeId
     *
     * @return bool
     */
    public function nodeExists($tableName, $nodeId)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$tableName} WHERE id = ?", [$nodeId]) > 0;
    }

    /**
     * @param string $tableName
     * @param int    $rightId
     * @param int    $blockId
     *
     * @return bool
     */
    public function nextNodeExists($tableName, $rightId, $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND block_id = ?' : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$tableName} WHERE right_id = ? {$where}", [$rightId, $blockId]) > 0;
    }

    /**
     * @param string $tableName
     * @param int    $rightId
     * @param int    $blockId
     *
     * @return bool
     */
    public function previousNodeExists($tableName, $rightId, $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND block_id = ?' : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$tableName} WHERE left_id = ? {$where}", [$rightId, $blockId]) > 0;
    }

    /**
     * @param string $tableName
     * @param int    $nodeId
     *
     * @return array
     */
    public function fetchNodeById($tableName, $nodeId)
    {
        return $this->db->fetchAssoc("SELECT `root_id`, `left_id`, `right_id` FROM {$tableName} WHERE id = ?", [$nodeId]);
    }

    /**
     * @param string $tableName
     * @param int    $leftId
     * @param int    $rightId
     *
     * @return bool
     */
    public function nodeIsRootItem($tableName, $leftId, $rightId)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$tableName} WHERE left_id < ? AND right_id > ?", [$leftId, $rightId]) == 0;
    }

    /**
     * @param string $tableName
     * @param int    $leftId
     * @param int    $rightId
     *
     * @return int
     */
    public function fetchParentNode($tableName, $leftId, $rightId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT `id` FROM {$tableName} WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @param string $tableName
     * @param int    $leftId
     * @param int    $rightId
     *
     * @return int
     */
    public function fetchRootNode($tableName, $leftId, $rightId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT `id` FROM {$tableName} WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @param string $tableName
     * @param int    $blockId
     *
     * @return int
     */
    public function fetchMaximumRightIdByBlockId($tableName, $blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MAX(`right_id`) FROM {$tableName} WHERE block_id = ?",
            [$blockId]
        );
    }

    /**
     * @param string $tableName
     *
     * @return mixed
     */
    public function fetchMaximumRightId($tableName)
    {
        return (int)$this->db->fetchColumn("SELECT MAX(`right_id`) FROM {$tableName}");
    }

    /**
     * @param string $tableName
     * @param int    $blockId
     *
     * @return int
     */
    public function fetchMinimumLeftIdByBlockId($tableName, $blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$tableName} WHERE block_id = ?",
            [$blockId]
        );
    }
}
