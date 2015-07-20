<?php
namespace ACP3\Core\NestedSet;

use ACP3\Core\DB;

/**
 * Class Model
 * @package ACP3\Core\NestedSet
 */
class Model extends \ACP3\Core\Model
{
    /**
     * Die aktuelle Seite mit allen untergeordneten Seiten selektieren
     *
     * @param string $tableName
     * @param int    $id
     * @param bool   $enableBlocks
     *
     * @return array
     */
    public function fetchNodeWithSiblings($tableName, $id, $enableBlocks)
    {
        return $this->db->fetchAll(
            'SELECT n.id, n.root_id, n.left_id, n.right_id' . ($enableBlocks === true ? ', n.block_id' : '') . " FROM {$tableName} AS p, {$tableName} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$id]
        );
    }

    /**
     * @param string $tableName
     * @param int    $id
     *
     * @return bool
     */
    public function nodeExists($tableName, $id)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$tableName} WHERE id = ?", [$id]) > 0;
    }

    /**
     * @param string $tableName
     * @param int    $id
     *
     * @return array
     */
    public function fetchNodeById($tableName, $id)
    {
        return $this->db->fetchAssoc("SELECT `root_id`, `left_id`, `right_id` FROM {$tableName} WHERE id = ?", [$id]);
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
     * @param int    $id
     *
     * @return int
     */
    public function fetchMinimumLeftIdByBlockId($tableName, $id)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$tableName} WHERE block_id = ?",
            [$id]
        );
    }
}