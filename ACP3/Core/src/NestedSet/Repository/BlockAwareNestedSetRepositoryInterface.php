<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Repository;

interface BlockAwareNestedSetRepositoryInterface
{
    /**
     * @return array<string, mixed>[]
     */
    public function fetchAllSortedByBlock(): array;
}
