<?php
namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions\Model\ResourceRepository;
use ACP3\Modules\ACP3\Permissions\Model\RoleRepository;
use ACP3\Modules\ACP3\Permissions\Model\RuleRepository;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\Permissions
 */
class Cache extends Core\Modules\AbstractCacheStorage
{
    const CACHE_ID_RESOURCES = 'acl_resources';
    const CACHE_ID_ROLES = 'acl_roles';
    const CACHE_ID_RULES = 'acl_rules_';

    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RoleRepository
     */
    protected $roleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository
     */
    protected $resourceRepository;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RuleRepository
     */
    protected $ruleRepository;

    /**
     * @param \ACP3\Core\Cache                                        $cache
     * @param \ACP3\Modules\ACP3\Permissions\Model\RoleRepository     $roleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\ResourceRepository $resourceRepository
     * @param \ACP3\Modules\ACP3\Permissions\Model\RuleRepository     $ruleRepository
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
     * @return array
     */
    public function getResourcesCache()
    {
        if ($this->cache->contains(static::CACHE_ID_RESOURCES) === false) {
            $this->saveResourcesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_RESOURCES);
    }

    /**
     * Erstellt den Cache für alle existierenden Ressourcen
     *
     * @return boolean
     */
    public function saveResourcesCache()
    {
        $resources = $this->resourceRepository->getAllResources();
        $cResources = count($resources);
        $data = [];

        for ($i = 0; $i < $cResources; ++$i) {
            $area = $resources[$i]['area'];
            if (isset($data[$area]) === false) {
                $data[$area] = [];
            }
            $path = $resources[$i]['module_name'] . '/' . $resources[$i]['controller'] . '/' . $resources[$i]['page'] . '/';
            $path .= !empty($resources[$i]['params']) ? $resources[$i]['params'] . '/' : '';
            $data[$area][$path] = [
                'resource_id' => $resources[$i]['resource_id'],
                'privilege_id' => $resources[$i]['privilege_id'],
                'key' => $resources[$i]['privilege_name'],
            ];
        }
        return $this->cache->save(static::CACHE_ID_RESOURCES, $data);
    }

    /**
     * @return bool|mixed|string
     */
    public function getRolesCache()
    {
        if ($this->cache->contains(static::CACHE_ID_ROLES) === false) {
            $this->saveRolesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_ROLES);
    }

    /**
     * Setzt den Cache für alle existierenden Rollen
     *
     * @return boolean
     */
    public function saveRolesCache()
    {
        $roles = $this->roleRepository->getAllRoles();
        $cRoles = count($roles);

        for ($i = 0; $i < $cRoles; ++$i) {
            // Bestimmen, ob die Seite die Erste und/oder Letzte eines Knotens ist
            $first = $last = true;
            if ($i > 0) {
                for ($j = $i - 1; $j >= 0; --$j) {
                    if ($roles[$j]['parent_id'] === $roles[$i]['parent_id']) {
                        $first = false;
                        break;
                    }
                }
            }

            for ($j = $i + 1; $j < $cRoles; ++$j) {
                if ($roles[$i]['parent_id'] === $roles[$j]['parent_id']) {
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
     * @param array $roles
     *
     * @return bool|mixed|string
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
     * Setzt den Cache für die einzelnen Berechtigungen einer Rolle
     *
     * @param array $roles
     *
     * @return boolean
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
     * @param array  $rule
     * @param string $privilegeKey
     *
     * @return bool
     */
    protected function hasAccess(array $rule, $privilegeKey)
    {
        return $rule['permission'] == Core\ACL\PermissionEnum::PERMIT_ACCESS
        || ($rule['permission'] == Core\ACL\PermissionEnum::INHERIT_ACCESS
            && $this->getPermissionValue($privilegeKey, $rule['role_id']) == Core\ACL\PermissionEnum::PERMIT_ACCESS);
    }

    /**
     * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle
     *
     * @param string  $privilegeKey
     * @param integer $roleId
     *
     * @return integer
     */
    protected function getPermissionValue($privilegeKey, $roleId)
    {
        $value = $this->roleRepository->getPermissionByKeyAndRoleId($privilegeKey, $roleId);
        return isset($value['permission']) ? $value['permission'] : Core\ACL\PermissionEnum::DENY_ACCESS;
    }
}
