<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\NestedSet\Model\Repository;

interface BlockAwareNestedSetRepositoryInterface
{
    /**
     * @return array
     */
    public function fetchAllSortedByBlock();
}
