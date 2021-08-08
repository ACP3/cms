<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL\Repository;

use ACP3\Core\Repository\RepositoryInterface;

interface AclPermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPermissionsByRoleIds(array $roleIds): array;

    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPermissionsByRoleIdsWithInheritance(array $roleIds): array;
}
