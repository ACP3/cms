<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

interface PermissionServiceInterface
{
    /**
     * Returns the cache of all the registered resources.
     * This will also only return the resources of the currently installed and active modules.
     */
    public function getResources(): array;

    public function getRoles(): array;

    /**
     * @param int[] $roleIds
     */
    public function getRules(array $roleIds): array;

    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<int, int>>
     */
    public function getPermissions(array $roleIds): array;
}
