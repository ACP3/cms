<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL\Repository;

use ACP3\Core\Repository\RepositoryInterface;

interface UserRoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array<string, mixed>[]
     */
    public function getRolesByUserId(int $userId): array;
}
