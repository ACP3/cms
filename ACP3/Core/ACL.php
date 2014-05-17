<?php
namespace ACP3\Core;

/**
 * Access control lists
 *
 * @author Tino Goratsch
 */
class ACL
{
    /**
     * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen
     *
     * @var array
     */
    protected static $privileges = array();
    /**
     * ID des eingeloggten Users
     *
     * @var array
     */
    protected static $userId = 0;
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen
     *
     * @var array
     */
    protected static $userRoles = array();
    /**
     * Array mit allen registrierten Ressourcen
     *
     * @var array
     */
    protected static $resources = array();

    /**
     * Konstruktor - erzeugt die ACL für den jeweiligen User
     *
     * @param integer $userId
     */
    public static function initialize($userId)
    {
        self::$userId = $userId;
        self::$userRoles = self::getUserRoles();
        self::$resources = self::getResources();
        self::$privileges = self::getRules(self::$userRoles);
    }

    /**
     * Erstellt den Cache für alle existierenden Ressourcen
     *
     * @return boolean
     */
    public static function setResourcesCache()
    {
        $resources = Registry::get('Db')->fetchAll('SELECT r.id AS resource_id, r.module_id, m.name AS module, r.area, r.controller, r.page, r.params, r.privilege_id, p.key FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'acl_privileges AS p ON(r.privilege_id = p.id) JOIN ' . DB_PRE . 'modules AS m ON(r.module_id = m.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.page ASC');
        $c_resources = count($resources);
        $data = array();

        for ($i = 0; $i < $c_resources; ++$i) {
            $area = $resources[$i]['area'];
            if (isset($data[$area]) === false) {
                $data[$area] = array();
            }
            $path = $resources[$i]['module'] . '/' . $resources[$i]['controller'] . '/' . $resources[$i]['page'] . '/';
            $path .= !empty($resources[$i]['params']) ? $resources[$i]['params'] . '/' : '';
            $data[$area][$path] = array(
                'resource_id' => $resources[$i]['resource_id'],
                'privilege_id' => $resources[$i]['privilege_id'],
                'key' => $resources[$i]['key'],
            );
        }
        return Cache::create('acl_resources', $data, 'acl');
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
     *
     * @return array
     */
    public static function getResources()
    {
        if (Cache::check('acl_resources', 'acl') === false) {
            self::setResourcesCache();
        }

        return Cache::output('acl_resources', 'acl');
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *    ID des Benutzers, dessen Rollen ausgegeben werden sollen
     * @param integer $mode
     *  1 = IDs der Rollen ausgeben
     *    2 = Namen der Rollen ausgeben
     * @return array
     */
    public static function getUserRoles($userId = 0, $mode = 1)
    {
        if ($userId === 0) {
            $userId = self::$userId;
        }

        $field = $mode === 2 ? 'r.name' : 'r.id';
        $key = substr($field, 2);
        $userRoles = Registry::get('Db')->fetchAll('SELECT ' . $field . ' FROM ' . DB_PRE . 'acl_user_roles AS ur JOIN ' . DB_PRE . 'acl_roles AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC', array($userId), array(\PDO::PARAM_INT));
        $c_userRoles = count($userRoles);
        $roles = array();

        for ($i = 0; $i < $c_userRoles; ++$i) {
            $roles[] = $userRoles[$i][$key];
        }
        return $roles;
    }

    /**
     * Setzt den Cache für alle existierenden Rollen
     *
     * @return boolean
     */
    public static function setRolesCache()
    {
        $roles = Registry::get('Db')->fetchAll('SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . DB_PRE . 'acl_roles AS p, ' . DB_PRE . 'acl_roles AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
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

        return Cache::create('acl_all_roles', $roles, 'acl');
    }

    /**
     * Setzt den Cache für die einzelnen Berechtigungen einer Rolle
     *
     * @param array $roles
     *    Array mit den IDs der zu cachenden Rollen
     * @return boolean
     */
    public static function setRulesCache(array $roles)
    {
        // Berechtigungen einlesen, auf die der Benutzer laut seinen Rollen Zugriff hat
        $rules = Registry::get('Db')->executeQuery('SELECT ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM ' . DB_PRE . 'acl_rules AS ru JOIN ' . DB_PRE . 'modules AS m ON (ru.module_id = m.id) JOIN ' . DB_PRE . 'acl_privileges AS p ON(ru.privilege_id = p.id) WHERE ru.role_id IN(?)',
            array($roles), array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY))->fetchAll();
        $c_rules = count($rules);
        $privileges = array();
        for ($i = 0; $i < $c_rules; ++$i) {
            $key = strtolower($rules[$i]['key']);
            $privileges[$rules[$i]['module_name']][$key] = array(
                'id' => $rules[$i]['privilege_id'],
                'description' => $rules[$i]['description'],
                'permission' => $rules[$i]['permission'],
                'access' => $rules[$i]['permission'] == 1 || ($rules[$i]['permission'] == 2 && self::getPermissionValue($key, $rules[$i]['role_id']) == 1) ? true : false,
            );
        }

        return Cache::create('acl_rules_' . implode(',', $roles), $privileges, 'acl');
    }

    /**
     * Gibt alle existieren Rollen aus
     *
     * @return array
     */
    public static function getAllRoles()
    {
        if (Cache::check('acl_all_roles', 'acl') === false) {
            self::setRolesCache();
        }

        return Cache::output('acl_all_roles', 'acl');
    }

    /**
     * Gibt alle existierenden Privilegien/Berechtigungen aus
     *
     * @return array
     */
    public static function getAllPrivileges()
    {
        return Registry::get('Db')->fetchAll('SELECT id, `key`, description FROM ' . DB_PRE . 'acl_privileges ORDER BY `key` ASC');
    }

    /**
     * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle
     *
     * @param string $key
     *    Schlüssel der Privilegie
     * @param integer $roleId
     *    ID der Rolle, dessen übergeordnete Rolle sucht werden soll
     * @return integer
     */
    protected static function getPermissionValue($key, $roleId)
    {
        $value = Registry::get('Db')->fetchAssoc('SELECT ru.permission FROM ' . DB_PRE . 'acl_roles AS r, ' . DB_PRE . 'acl_roles AS parent JOIN ' . DB_PRE . 'acl_rules AS ru ON(parent.id = ru.role_id) JOIN ' . DB_PRE . 'acl_privileges AS p ON(ru.privilege_id = p.id) WHERE r.id = ? AND p.key = ? AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1', array($roleId, $key));
        return isset($value['permission']) ? $value['permission'] : 0;
    }

    /**
     * Gibt die Rollen-Berechtigungen aus
     *
     * @param array $roles
     *    Array mit den IDs der Rollen
     * @return boolean
     */
    public static function getRules(array $roles)
    {
        $filename = 'acl_rules_' . implode(',', $roles);
        if (Cache::check($filename, 'acl') === false) {
            self::setRulesCache($roles);
        }

        return Cache::output($filename, 'acl');
    }

    /**
     * Gibt aus ob dem Benutzer die jeweilige Rolle zugeordnet ist
     *
     * @param integer $roleId
     *    ID der zu überprüfenden Rolle
     * @return boolean
     */
    public static function userHasRole($roleId)
    {
        return in_array($roleId, self::$userRoles);
    }

    /**
     * Gibt aus, ob ein Benutzer die Berechtigung auf eine Privilegie besitzt
     *
     * @param $module
     * @param string $key
     *    The key of the privilege
     * @return boolean
     */
    public static function userHasPrivilege($module, $key)
    {
        $key = strtolower($key);
        if (isset(self::$privileges[$module][$key])) {
            return self::$privileges[$module][$key]['access'];
        }
        return false;
    }

    /**
     * Gibt aus, ob ein Benutzer berichtigt ist, eine Ressource zu betreten
     *
     * @param string $resource
     *    The path of a resource in the format of an internal ACP3 url
     * @return boolean
     */
    public static function canAccessResource($resource)
    {
        $resourceArray = explode('/', $resource);

        if (empty($resourceArray[2]) === true) {
            $resourceArray[2] = 'index';
        }
        if (empty($resourceArray[3]) === true) {
            $resourceArray[3] = 'index';
        }

        $area = $resourceArray[0];
        $resource = $resourceArray[1] . '/' . $resourceArray[2] . '/' . $resourceArray[3] . '/';

        if (isset(self::$resources[$area][$resource])) {
            $module = $resourceArray[1];
            $key = self::$resources[$area][$resource]['key'];
            return self::userHasPrivilege($module, $key) === true || Registry::get('Auth')->isSuperUser() === true;
        }
        return false;
    }
}