<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;

abstract class NestedSetRepository extends AbstractRepository
{
    const BLOCK_COLUMN_NAME = 'block_id';

    /**
     * Fetch the given node with all its parent nodes.
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchNodeWithParents(int $nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id <= p.left_id AND n.right_id >= p.left_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * Die aktuelle Seite mit allen untergeordneten Seiten selektieren.
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchNodeWithSiblings(int $nodeId)
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchNextNodeWithSiblings(int $leftId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$leftId]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchPrevNodeWithSiblings(int $rightId)
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$rightId]
        );
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nodeExists(int $nodeId)
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$nodeId]) > 0;
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nextNodeExists(int $rightId, int $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND ' . static::BLOCK_COLUMN_NAME . ' = ?' : '';

        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE right_id = ? {$where}",
                [$rightId, $blockId]
            ) > 0;
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function previousNodeExists(int $rightId, int $blockId = 0)
    {
        $where = ($blockId !== 0) ? ' AND ' . static::BLOCK_COLUMN_NAME . ' = ?' : '';

        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id = ? {$where}",
                [$rightId, $blockId]
            ) > 0;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchNodeById(int $nodeId)
    {
        return $this->db->fetchAssoc(
            "SELECT `root_id`, `left_id`, `right_id` FROM {$this->getTableName()} WHERE id = ?",
            [$nodeId]
        );
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function nodeIsRootItem(int $leftId, int $rightId)
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ?",
                [$leftId, $rightId]
            ) == 0;
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchParentNode(int $leftId, int $rightId)
    {
        return (int) $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchRootNode(int $leftId, int $rightId)
    {
        return (int) $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchMaximumRightIdByBlockId(int $blockId)
    {
        return (int) $this->db->fetchColumn(
            "SELECT MAX(`right_id`) FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . ' = ?',
            [$blockId]
        );
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchMaximumRightId()
    {
        return (int) $this->db->fetchColumn("SELECT MAX(`right_id`) FROM {$this->getTableName()}");
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchMinimumLeftIdByBlockId(int $blockId)
    {
        return (int) $this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . ' = ?',
            [$blockId]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}");
    }
}
