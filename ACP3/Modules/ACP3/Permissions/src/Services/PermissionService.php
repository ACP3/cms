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
use ACP3\Modules\ACP3\Permissions\Repository\AclRuleRepository;

class PermissionService implements PermissionServiceInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Repository\AclRoleRepository
     */
    private $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Repository\AclResourceRepository
     */
    private $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Repository\AclRuleRepository
     */
    private $ruleRepository;
    /**
     * @var AclPermissionRepository
     */
    private $permissionRepository;

    public function __construct(
        AclRoleRepository $roleRepository,
        AclResourceRepository $resourceRepository,
        AclRuleRepository $ruleRepository,
        AclPermissionRepository $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->ruleRepository = $ruleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * {@inheritdoc}
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
                'resource_id' => $resource['resource_id'],
                'privilege_id' => $resource['privilege_id'],
                'key' => $resource['privilege_name'],
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRules(array $roleIds): array
    {
        $rules = [];
        foreach ($this->ruleRepository->getAllRulesByRoleIds($roleIds) as $rule) {
            $privilegeKey = strtolower($rule['key']);
            $rules[$rule['module_name']][$privilegeKey] = [
                'id' => $rule['privilege_id'],
                'description' => $rule['description'],
                'permission' => $rule['permission'],
                'access' => $this->hasAccess($rule, $privilegeKey),
            ];
        }

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPermissions(array $roleIds): array
    {
        $permissions = [];

        foreach ($this->permissionRepository->getPermissionsByRoleIds($roleIds) as $permission) {
            if (!\array_key_exists((int) $permission['role_id'], $permissions)) {
                $permissions[(int) $permission['role_id']] = [];
            }

            $permissions[(int) $permission['role_id']][(int) $permission['resource_id']] = (int) $permission['permission'];
        }

        return $permissions;
    }

    private function hasAccess(array $rule, string $privilegeKey): bool
    {
        return $rule['permission'] == PermissionEnum::PERMIT_ACCESS
            || ($rule['permission'] == PermissionEnum::INHERIT_ACCESS
                && $this->getPermissionValue($privilegeKey, $rule['module_id'], $rule['role_id']) === PermissionEnum::PERMIT_ACCESS);
    }

    /**
     * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle.
     */
    private function getPermissionValue(string $privilegeKey, int $moduleId, int $roleId): int
    {
        $permission = $this->roleRepository->getPermissionByKeyAndRoleId($privilegeKey, $moduleId, $roleId);

        return $permission ?? PermissionEnum::DENY_ACCESS;
    }
}
