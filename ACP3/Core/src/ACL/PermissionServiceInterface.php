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
     *
     * @return array<string, array<string, array<string, int>>>
     */
    public function getResources(): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRoles(): array;

    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<int, PermissionEnum>>
     */
    public function getPermissions(array $roleIds): array;

    /**
     * @param int[] $roleIds
     *
     * @return PermissionEnum[]
     */
    public function getPermissionsWithInheritance(array $roleIds): array;
}
