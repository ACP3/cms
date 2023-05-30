<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Services;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\PermissionServiceInterface;
use ACP3\Modules\ACP3\Permissions\Repository\AclPermissionRepository;
use ACP3\Modules\ACP3\Permissions\Repository\AclResourceRepository;
use ACP3\Modules\ACP3\Permissions\Repository\AclRoleRepository;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(private readonly AclRoleRepository $roleRepository, private readonly AclResourceRepository $resourceRepository, private readonly AclPermissionRepository $permissionRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getResources(): array
    {
        $resources = $this->resourceRepository->getAllResources();
        $data = [];

        foreach ($resources as $resource) {
            $area = $resource['area'];
            if (isset($data[$area]) === false) {
                $data[$area] = [];
            }
            $path = $resource['module_name'] . '/' . $resource['controller'] . '/' . $resource['page'] . '/';
            $path .= !empty($resource['params']) ? $resource['params'] . '/' : '';
            $data[$area][$path] = [
                'resource_id' => (int) $resource['resource_id'],
            ];
        }

        return $data;
    }

    public function getRoles(): array
    {
        $roles = $this->roleRepository->getAllRoles();
        $cRoles = \count($roles);

        foreach ($roles as $i => $role) {
            // Bestimmen, ob die Seite die Erste und/oder Letzte eines Knotens ist
            $first = $last = true;
            if ($i > 0) {
                for ($j = $i - 1; $j >= 0; --$j) {
                    if ($roles[$j]['parent_id'] === $role['parent_id']) {
                        $first = false;

                        break;
                    }
                }
            }

            for ($j = $i + 1; $j < $cRoles; ++$j) {
                if ($role['parent_id'] === $roles[$j]['parent_id']) {
                    $last = false;

                    break;
                }
            }

            $roles[$i]['first'] = $first;
            $roles[$i]['last'] = $last;
        }

        return $roles;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPermissions(array $roleIds): array
    {
        $permissions = [];

        foreach ($this->permissionRepository->getPermissionsByRoleIds($roleIds) as $permission) {
            if (!\array_key_exists((int) $permission['role_id'], $permissions)) {
                $permissions[(int) $permission['role_id']] = [];
            }

            $permissions[(int) $permission['role_id']][(int) $permission['resource_id']] = PermissionEnum::tryFrom((int) $permission['permission']);
        }

        return $permissions;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPermissionsWithInheritance(array $roleIds): array
    {
        $permissions = [];

        foreach ($this->permissionRepository->getPermissionsByRoleIdsWithInheritance($roleIds) as $permission) {
            $permissions[(int) $permission['resource_id']] = $permission['permission'] === null ? PermissionEnum::DENY_ACCESS : PermissionEnum::tryFrom((int) $permission['permission']);
        }

        return $permissions;
    }
}
