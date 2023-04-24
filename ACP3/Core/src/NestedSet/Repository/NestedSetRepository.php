<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Repository;

use ACP3\Core\Repository\AbstractRepository;
use Doctrine\DBAL\ArrayParameterType;

abstract class NestedSetRepository extends AbstractRepository
{
    public const BLOCK_COLUMN_NAME = 'block_id';

    /**
     * Fetch the given node with all its parent nodes.
     *
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchNodeWithParents(int $nodeId): array
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id <= p.left_id AND n.right_id >= p.left_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * Die aktuelle Seite mit allen untergeordneten Seiten selektieren.
     *
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchNodeWithSiblings(int $nodeId): array
    {
        return $this->db->fetchAll(
            "SELECT n.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS n WHERE p.id = ? AND n.left_id BETWEEN p.left_id AND p.right_id ORDER BY n.left_id ASC",
            [$nodeId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchNextNodeWithSiblings(int $leftId): array
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.left_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$leftId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchPrevNodeWithSiblings(int $rightId): array
    {
        return $this->db->fetchAll(
            "SELECT c.* FROM {$this->getTableName()} AS p, {$this->getTableName()} AS c WHERE p.right_id = ? AND c.left_id BETWEEN p.left_id AND p.right_id ORDER BY c.left_id ASC",
            [$rightId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function nodeExists(int $nodeId): bool
    {
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = ?", [$nodeId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function nextNodeExists(int $rightId, int $blockId = 0): bool
    {
        $where = ($blockId !== 0) ? ' AND ' . static::BLOCK_COLUMN_NAME . ' = :blockId' : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE right_id = :rightId {$where}",
            ['rightId' => $rightId, 'blockId' => $blockId]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function previousNodeExists(int $rightId, int $blockId = 0): bool
    {
        $where = ($blockId !== 0) ? ' AND ' . static::BLOCK_COLUMN_NAME . ' = :blockId' : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id = :leftId {$where}",
            ['leftId' => $rightId, 'blockId' => $blockId]
        ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchNodeById(int $nodeId): array
    {
        return $this->db->fetchAssoc(
            "SELECT `root_id`, `left_id`, `right_id` FROM {$this->getTableName()} WHERE id = ?",
            [$nodeId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function nodeIsRootItem(int $leftId, int $rightId): bool
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ?",
            [$leftId, $rightId]
        ) === 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchParentNode(int $leftId, int $rightId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchRootNode(int $leftId, int $rightId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT `id` FROM {$this->getTableName()} WHERE left_id < ? AND right_id >= ? ORDER BY left_id ASC LIMIT 1",
            [$leftId, $rightId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchMaximumRightIdByBlockId(int $blockId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT MAX(`right_id`) FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . ' = ?',
            [$blockId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchMaximumRightId(): int
    {
        return (int) $this->db->fetchColumn("SELECT MAX(`right_id`) FROM {$this->getTableName()}");
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchMinimumLeftIdByBlockId(int $blockId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT MIN(`left_id`) AS left_id FROM {$this->getTableName()} WHERE " . static::BLOCK_COLUMN_NAME . ' = ?',
            [$blockId]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}");
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateRootIdAndParentIdOfNode(int $rootId, int $parentId, int $nodeId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET root_id = ?, parent_id = ? WHERE id = ?",
            [
                $rootId,
                $parentId,
                $nodeId,
            ]
        );
    }

    /**
     * @param int[] $nodeIds
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function moveNodesWithinTree(int $offsetLeftId, int $offsetRightId, array $nodeIds): bool
    {
        return (bool) $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET left_id = left_id + :offsetLeftId, right_id = right_id + :offsetRightId WHERE id IN(:nodeIds)",
            ['offsetLeftId' => $offsetLeftId, 'offsetRightId' => $offsetRightId, 'nodeIds' => $nodeIds],
            ['nodeIds' => ArrayParameterType::INTEGER]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function moveSubsequentNodesOfBlock(int $offset, int $leftIdConstraint, int $blockId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ? AND " . static::BLOCK_COLUMN_NAME . ' = ?',
            [$offset, $offset, $leftIdConstraint, $blockId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function adjustParentNodesAfterSeparation(int $diff, int $leftId, int $rightId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function adjustParentNodesAfterInsert(int $diff, int $leftId, int $rightId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function adjustFollowingNodesAfterSeparation(int $diff, int $leftId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
            [$diff, $diff, $leftId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function adjustFollowingNodesAfterInsert(int $diff, int $leftId): void
    {
        $this->db->getConnection()->executeStatement(
            "UPDATE {$this->getTableName()} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?",
            [$diff, $diff, $leftId]
        );
    }
}
