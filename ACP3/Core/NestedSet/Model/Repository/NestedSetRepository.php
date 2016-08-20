<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
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
    /**
     * Die aktuelle Seite mit allen untergeordneten Seiten selektieren
     *
     * @param int $nodeId
     * @return array
     */
    public function fetchNodeWithSiblings($nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * @param int $leftId
     * @return array
     */
    public function fetchNextNodeWithSiblings($leftId)
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
    public function fetchPrevNodeWithSiblings($rightId)
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
    public function nodeExists($nodeId)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$nodeId]) > 0;
    }

    /**
     * @param int $rightId
     * @param int $blockId
     * @return bool
     */
    public function nextNodeExists($rightId, $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND block_id = ?' : '';
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE right_id = ? {$where}",
            [$rightId, $blockId]
        ) > 0;
    }

    /**
     * @param int $rightId
     * @param int $blockId
     * @return bool
     */
    public function previousNodeExists($rightId, $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND block_id = ?' : '';
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id = ? {$where}",
            [$rightId, $blockId]
        ) > 0;
    }

    /**
     * @param int $nodeId
     * @return array
     */
    public function fetchNodeById($nodeId)
    {
        return $this->db->fetchAssoc("SELECT `root_id`, `left_id`, `right_id` FROM {$this->getTableName()} WHERE id = ?",
            [$nodeId]);
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return bool
     */
    public function nodeIsRootItem($leftId, $rightId)
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ?",
            [$leftId, $rightId]
        ) == 0;
    }

    /**
     * @param int $leftId
     * @param int $rightId
     * @return int
     */
    public function fetchParentNode($leftId, $rightId)
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
    public function fetchRootNode($leftId, $rightId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @param int $blockId
     * @return int
     */
    public function fetchMaximumRightIdByBlockId($blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MAX(`right_id`) FROM {$this->getTableName()} WHERE block_id = ?",
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
    public function fetchMinimumLeftIdByBlockId($blockId)
    {
        return (int)$this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$this->getTableName()} WHERE block_id = ?",
            [$blockId]
        );
    }
}
