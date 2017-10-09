<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;

/**
 * Class NestedSetRepository
 * @package ACP3\Core\NestedSet\Model\Repository
 */
abstract class NestedSetRepository extends AbstractRepository
{
    const BLOCK_COLUMN_NAME = 'block_id';

    /**
     * Fetches the given node with all its siblings
     *
     * @param int $nodeId
     * @return array
     */
    public function fetchNodeWithSiblings(int $nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * Fetch the given node with all its parent nodes
     *
     * @param int $nodeId
     * @return array
     */
    public function fetchNodeWithParents(int $nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id <= p.left_id AND n.right_id >= p.left_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * @param int $leftId
     * @return array
     */
    public function fetchNextNodeWithSiblings(int $leftId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$leftId]
        );
    }

    /**
     * @param int $rightId
     * @return array
     */
    public function fetchPrevNodeWithSiblings(int $rightId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$rightId]
        );
    }

    /**
     * @param int $nodeId
     * @return bool
     */
    public function nodeExists(int $nodeId)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$nodeId]) > 0;
    }

    /**
     * @param int $rightId
     * @param int $blockId
     * @return bool
     */
    public function nextNodeExists(int $rightId, int $blockId = 0)
    {
        $where = ($blockId !== 0) ? " AND " . static::BLOCK_COLUMN_NAME . " = ?" : '';
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE right_id = ? {$where}", [$rightId, $blockId]
            ) > 0;
    }

    /**
     * @param int $rightId
     * @param int $blockId
     * @return bool
     */
    public function previousNodeExists(int $rightId, int $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND ' . static::BLOCK_COLUMN_NAME . ' = ?' : '';
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id = ? {$where}", [$rightId, $blockId]
            ) > 0;
    }

    /**
     * @param int $nodeId
     * @return array
     */
    public function fetchNodeById(int $nodeId)
    {
        return $this->db->fetchAssoc(
            "SELECT `root_id`, `left_id`, `right_id` FROM {$this->getTableName()} WHERE id = ?",
            [$nodeId]
        );
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return bool
     */
    public function nodeIsRootItem(int $leftId, int $rightId)
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ?", [$leftId, $rightId]
            ) == 0;
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return int
     */
    public function fetchParentNode(int $leftId, int $rightId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return int
     */
    public function fetchRootNode(int $leftId, int $rightId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ? AND parent_id = ?",
            [$leftId, $rightId, 0]
        );
    }

    /**
     * @param int $blockId
     * @return int
     */
    public function fetchMaximumRightIdByBlockId(int $blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MAX(`right_id`) FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . " = ?",
            [$blockId]
        );
    }

    /**
     * @return int
     */
    public function fetchMaximumRightId()
    {
        return (int)$this->db->fetchColumn("SELECT MAX(`right_id`) FROM {$this->getTableName()}");
    }

    /**
     * @param int $blockId
     * @return int
     */
    public function fetchMinimumLeftIdByBlockId(int $blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . " = ?",
            [$blockId]
        );
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}");
    }
}
