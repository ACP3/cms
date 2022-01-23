<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL\Repository;

use ACP3\Core\Repository\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array<array<string, mixed>>
     */
    public function getAllRoles(): array;
}
