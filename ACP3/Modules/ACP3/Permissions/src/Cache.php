<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Core;
use ACP3\Core\ACL\PermissionCacheInterface;
use ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository;

class Cache extends Core\Modules\AbstractCacheStorage implements PermissionCacheInterface
{
    public const CACHE_ID_RESOURCES = 'acl_resources';
    public const CACHE_ID_ROLES = 'acl_roles';
    public const CACHE_ID_RULES = 'acl_rules_';

    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository
     */
    private $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\ResourceRepository
     */
    private $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RuleRepository
     */
    private $ruleRepository;

    /**
     * @param \ACP3\Core\Cache $cache
     */
    public function __construct(
        Core\Cache $cache,
        RoleRepository $roleRepository,
        ResourceRepository $resourceRepository,
        RuleRepository $ruleRepository
    ) {
        parent::__construct($cache);

        $this->roleRepository = $roleRepository;
        $this->resourceRepository = $resourceRepository;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesCache()
    {
        if ($this->cache->contains(static::CACHE_ID_RESOURCES) === false) {
            $this->saveResourcesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_RESOURCES);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveResourcesCache()
    {
        $resources = $this->resourceRepository->getAllResources();
        $data = [];

        foreach ($resources as $i => $resource) {
            $area = $resources[$i]['area'];
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

        return $this->cache->save(static::CACHE_ID_RESOURCES, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getRolesCache()
    {
        if ($this->cache->contains(static::CACHE_ID_ROLES) === false) {
            $this->saveRolesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_ROLES);
    }

    /**
     * {@inheritdoc}
     */
    public function saveRolesCache()
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

        return $this->cache->save(static::CACHE_ID_ROLES, $roles);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRulesCache(array $roles)
    {
        $filename = static::CACHE_ID_RULES . implode(',', $roles);
        if ($this->cache->contains($filename) === false) {
            $this->saveRulesCache($roles);
        }

        return $this->cache->fetch($filename);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveRulesCache(array $roles)
    {
        $privileges = [];
        foreach ($this->ruleRepository->getAllRulesByRoleIds($roles) as $rule) {
            $privilegeKey = strtolower($rule['key']);
            $privileges[$rule['module_name']][$privilegeKey] = [
                'id' => $rule['privilege_id'],
                'description' => $rule['description'],
                'permission' => $rule['permission'],
                'access' => $this->hasAccess($rule, $privilegeKey),
            ];
        }

        return $this->cache->save(static::CACHE_ID_RULES . implode(',', $roles), $privileges);
    }

    /**
     * @param string $privilegeKey
     *
     * @return bool
     */
    protected function hasAccess(array $rule, $privilegeKey)
    {
        return $rule['permission'] == Core\ACL\PermissionEnum::PERMIT_ACCESS
        || ($rule['permission'] == Core\ACL\PermissionEnum::INHERIT_ACCESS
            && $this->getPermissionValue($privilegeKey, $rule['module_id'], $rule['role_id']) == Core\ACL\PermissionEnum::PERMIT_ACCESS);
    }

    /**
     * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle.
     *
     * @return int
     */
    protected function getPermissionValue(string $privilegeKey, int $moduleId, int $roleId)
    {
        $permission = $this->roleRepository->getPermissionByKeyAndRoleId($privilegeKey, $moduleId, $roleId);

        return $permission ?? Core\ACL\PermissionEnum::DENY_ACCESS;
    }
}
