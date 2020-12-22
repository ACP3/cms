<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

use ACP3\Core\NestedSet\Model\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Core\NestedSet\Model\Repository\NestedSetRepository;

abstract class AbstractOperation
{
    /**
     * @var \ACP3\Core\NestedSet\Model\Repository\NestedSetRepository|BlockAwareNestedSetRepositoryInterface
     */
    protected $nestedSetRepository;

    public function __construct(
        NestedSetRepository $nestedSetRepository
    ) {
        $this->nestedSetRepository = $nestedSetRepository;
    }

    /**
     * Returns, whether the data repository is aware of the block handling.
     */
    protected function isBlockAware(): bool
    {
        return $this->nestedSetRepository instanceof BlockAwareNestedSetRepositoryInterface;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterSeparation(int $diff, int $leftId, int $rightId): void
    {
        $this->nestedSetRepository->adjustParentNodesAfterSeparation($diff, $leftId, $rightId);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustParentNodesAfterInsert(int $diff, int $leftId, int $rightId): void
    {
        $this->nestedSetRepository->adjustParentNodesAfterInsert($diff, $leftId, $rightId);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterSeparation(int $diff, int $leftId): void
    {
        $this->nestedSetRepository->adjustFollowingNodesAfterSeparation($diff, $leftId);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function adjustFollowingNodesAfterInsert(int $diff, int $leftId): void
    {
        $this->nestedSetRepository->adjustFollowingNodesAfterInsert($diff, $leftId);
    }
}
