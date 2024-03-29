<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Operation;

use ACP3\Core\NestedSet\Repository\BlockAwareNestedSetRepositoryInterface;
use ACP3\Core\NestedSet\Repository\NestedSetRepository;

abstract class AbstractOperation
{
    public function __construct(protected NestedSetRepository $nestedSetRepository)
    {
    }

    /**
     * Returns, whether the data repository is aware of the block handling.
     */
    protected function isBlockAware(): bool
    {
        return $this->nestedSetRepository instanceof BlockAwareNestedSetRepositoryInterface;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function adjustParentNodesAfterSeparation(int $diff, int $leftId, int $rightId): void
    {
        $this->nestedSetRepository->adjustParentNodesAfterSeparation($diff, $leftId, $rightId);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function adjustParentNodesAfterInsert(int $diff, int $leftId, int $rightId): void
    {
        $this->nestedSetRepository->adjustParentNodesAfterInsert($diff, $leftId, $rightId);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function adjustFollowingNodesAfterSeparation(int $diff, int $leftId): void
    {
        $this->nestedSetRepository->adjustFollowingNodesAfterSeparation($diff, $leftId);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function adjustFollowingNodesAfterInsert(int $diff, int $leftId): void
    {
        $this->nestedSetRepository->adjustFollowingNodesAfterInsert($diff, $leftId);
    }
}
