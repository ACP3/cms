<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\NestedSet\Operation;

use ACP3\Core\Database\Connection;
use ACP3\Core\NestedSet\Model\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Core\NestedSet\Model\Repository\NestedSetRepository;

/**
 * Class AbstractNestedSetOperation
 * @package ACP3\Core\NestedSet\Operation
 */
abstract class AbstractOperation
{
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\NestedSet\Model\Repository\NestedSetRepository
     */
    protected $nestedSetRepository;
    /**
     * @var bool
     */
    protected $isBlockAware;

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
        $this->isBlockAware = $nestedSetRepository instanceof BlockAwareNestedSetRepositoryInterface;
    }

    /**
     * @param int $diff
     * @param int $leftId
     * @param int $rightId
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterSeparation($diff, $leftId, $rightId)
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
    protected function adjustParentNodesAfterInsert($diff, $leftId, $rightId)
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
    protected function adjustFollowingNodesAfterSeparation($diff, $leftId)
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
    protected function adjustFollowingNodesAfterInsert($diff, $leftId)
    {
        $this->db->getConnection()->executeUpdate(
            "UPDATE {$this->nestedSetRepository->getTableName()} SET left_id = left_id + ?, right_id = right_id + ? WHERE left_id >= ?",
            [$diff, $diff, $leftId]
        );
    }
}
