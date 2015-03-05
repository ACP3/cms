<?php
namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Core;

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
     * @var Model
     */
    protected $permissionsModel;

    /**
     * @param Core\Cache $cache
     * @param Model $permissionsModel
     */
    public function __construct(
        Core\Cache $cache,
        Model $permissionsModel
    ) {
        parent::__construct($cache);

        $this->permissionsModel = $permissionsModel;
    }

    /**
     * @return bool|mixed|string
     */
    public function getResourcesCache()
    {
        if ($this->cache->contains(static::CACHE_ID_RESOURCES) === false) {
            $this->setResourcesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_RESOURCES);
    }

    /**
     * Erstellt den Cache für alle existierenden Ressourcen
     *
     * @return boolean
     */
    public function setResourcesCache()
    {
        $resources = $this->permissionsModel->getAllResources();
        $c_resources = count($resources);
        $data = [];

        for ($i = 0; $i < $c_resources; ++$i) {
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
            $this->setRolesCache();
        }

        return $this->cache->fetch(static::CACHE_ID_ROLES);
    }

    /**
     * Setzt den Cache für alle existierenden Rollen
     *
     * @return boolean
     */
    public function setRolesCache()
    {
        $roles = $this->permissionsModel->getAllRoles();
        $c_roles = count($roles);

        for ($i = 0; $i < $c_roles; ++$i) {
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

            for ($j = $i + 1; $j < $c_roles; ++$j) {
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
            $this->setRulesCache($roles);
        }

        return $this->cache->fetch($filename);
    }

    /**
     * Setzt den Cache für die einzelnen Berechtigungen einer Rolle
     *
     * @param array $roles
     *    Array mit den IDs der zu cachenden Rollen
     *
     * @return boolean
     */
    public function setRulesCache(array $roles)
    {
        // Berechtigungen einlesen, auf die der Benutzer laut seinen Rollen Zugriff hat
        $rules = $this->permissionsModel->getAllRulesByRoleIds($roles);
        $c_rules = count($rules);
        $privileges = [];
        for ($i = 0; $i < $c_rules; ++$i) {
            $key = strtolower($rules[$i]['key']);
            $privileges[$rules[$i]['module_name']][$key] = [
                'id' => $rules[$i]['privilege_id'],
                'description' => $rules[$i]['description'],
                'permission' => $rules[$i]['permission'],
                'access' => ($rules[$i]['permission'] == 1 || ($rules[$i]['permission'] == 2 && $this->_getPermissionValue($key, $rules[$i]['role_id']) == 1)),
            ];
        }

        return $this->cache->save(static::CACHE_ID_RULES . implode(',', $roles), $privileges);
    }

    /**
     * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle
     *
     * @param string  $key
     *    Schlüssel der Privilegie
     * @param integer $roleId
     *    ID der Rolle, dessen übergeordnete Rolle sucht werden soll
     *
     * @return integer
     */
    protected function _getPermissionValue($key, $roleId)
    {
        $value = $this->permissionsModel->getPermissionByKeyAndRoleId($key, $roleId);
        return isset($value['permission']) ? $value['permission'] : 0;
    }
}
