<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Operation;

use ACP3\Core\Database\Connection;
use ACP3\Core\NestedSet\Model\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Core\NestedSet\Model\Repository\NestedSetRepository;

abstract class AbstractOperation
{
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\NestedSet\Model\Repository\NestedSetRepository|BlockAwareNestedSetRepositoryInterface
     */
    protected $nestedSetRepository;

    /**
     * @param \ACP3\Core\Database\Connection $db
     * @param \ACP3\Core\NestedSet\Model\Repository\NestedSetRepository $nestedSetRepository
     */
    public function __construct(
        Connection $db,
        NestedSetRepository $nestedSetRepository
    ) {
        $this->db = $db;
        $this->nestedSetRepository = $nestedSetRepository;
    }

    /**
     * Returns, whether the data repository is aware of the block handling
     *
     * @return bool
     */
    protected function isBlockAware(): bool
    {
        return $this->nestedSetRepository instanceof BlockAwareNestedSetRepositoryInterface;
    }

    /**
     * @param int $diff
     * @param int $leftId
     * @param int $rightId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterSeparation(int $diff, int $leftId, int $rightId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET right_id = right_id - ? WHERE left_id < ? AND right_id > ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     * @param int $rightId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterInsert(int $diff, int $leftId, int $rightId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET right_id = right_id + ? WHERE left_id <= ? AND right_id >= ?",
            [$diff, $leftId, $rightId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterSeparation(int $diff, int $leftId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id - ?, right_id = right_id - ? WHERE left_id > ?",
            [$diff, $diff, $leftId]
        );
    }

    /**
     * @param int $diff
     * @param int $leftId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterInsert(int $diff, int $leftId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?",
            [$diff, $diff, $leftId]
        );
    }

    /**
     * Returns the name of the block
     *
     * @return string
     */
    protected function getBlockColumnName(): string
    {
        return $this->nestedSetRepository::BLOCK_COLUMN_NAME;
    }
}
