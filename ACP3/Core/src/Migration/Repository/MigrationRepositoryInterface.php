<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Migration\Repository;

use ACP3\Core\Repository\RepositoryInterface;

interface MigrationRepositoryInterface extends RepositoryInterface
{
    /**
     * @return string[]
     */
    public function findAllAlreadyExecutedMigrations(): array;
}
