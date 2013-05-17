<?php
/**
 * Access Control List
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

namespace ACP3\Core;

/**
 * ACL Klasse
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACL
{
	/**
	 * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen
	 *
	 * @var array
	 */
	private static $privileges = array();
	/**
	 * ID des eingeloggten Users
	 *
	 * @var array
	 */
	private static $userId = 0;
	/**
	 * Array mit den dem Benutzer zugewiesenen Rollen
	 *
	 * @var array
	 */
	private static $userRoles = array();
	/**
	 * Array mit allen registrierten Ressourcen
	 *
	 * @var array
	 */
	private static $resources = array();

	/**
	 * Konstruktor - erzeugt die ACL für den jeweiligen User
	 *
	 * @param integer $user_id
	 */
	public static function initialize($user_id)
	{
		self::$userId = $user_id;
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
		$resources = Registry::get('Db')->fetchAll('SELECT r.id AS resource_id, r.module_id, m.name AS module, r.page, r.params, r.privilege_id, p.key FROM ' . DB_PRE . 'acl_resources AS r JOIN ' . DB_PRE . 'acl_privileges AS p ON(r.privilege_id = p.id) JOIN ' . DB_PRE . 'modules AS m ON(r.module_id = m.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.page ASC');
		$c_resources = count($resources);
		$data = array();

		for ($i = 0; $i < $c_resources; ++$i) {
			$path = $resources[$i]['module'] . '/' . $resources[$i]['page'] . '/' . (!empty($resources[$i]['params']) ? $resources[$i]['params'] . '/' : '');
			$data[$path] = array(
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
		if (Cache::check('acl_resources', 'acl') === false)
			self::setResourcesCache();

		return Cache::output('acl_resources', 'acl');
	}
	/**
	 * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
	 *
	 * @param integer $user_id
	 *	ID des Benutzers, dessen Rollen ausgegeben werden sollen
	 * @param integer $mode
	 *  1 = IDs der Rollen ausgeben
	 *	2 = Namen der Rollen ausgeben
	 * @return array
	 */
	public static function getUserRoles($user_id = 0, $mode = 1)
	{
		$user_id = $user_id === 0 ? self::$userId : $user_id;
		$field = $mode === 2 ? 'r.name' : 'r.id';
		$key = substr($field, 2);
		$user_roles = Registry::get('Db')->fetchAll('SELECT ' . $field . ' FROM ' . DB_PRE . 'acl_user_roles AS ur JOIN ' . DB_PRE . 'acl_roles AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC', array($user_id), array(\PDO::PARAM_INT));
		$c_user_roles = count($user_roles);
		$roles = array();

		for ($i = 0; $i < $c_user_roles; ++$i) {
			$roles[] = $user_roles[$i][$key];
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
			$roles[$i]['name'] = $roles[$i]['name'];

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
	 *	Array mit den IDs der zu cachenden Rollen
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

		$impl_roles = implode(',', $roles);
		return Cache::create('acl_rules_' . $impl_roles, $privileges, 'acl');
	}
	/**
	 * Gibt alle existieren Rollen aus
	 *
	 * @return array
	 */
	public static function getAllRoles()
	{
		if (Cache::check('acl_all_roles', 'acl') === false)
			self::setRolesCache();

		return Cache::output('acl_all_roles', 'acl');
	}
	/**
	 * Gibt alle existierenden Privilegien/Berechtigungen aus
	 *
	 * @return array
	 */
	public static function getAllPrivileges()
	{
		$privileges = Registry::get('Db')->fetchAll('SELECT id, `key`, description FROM ' . DB_PRE . 'acl_privileges ORDER BY `key` ASC');
		return $privileges;
	}
	/**
	 * Ermittelt die Berechtigung einer Privilegie von einer übergeordneten Rolle
	 *
	 * @param string $key
	 *	Schlüssel der Privilegie
	 * @param integer $role_id
	 *	ID der Rolle, dessen übergeordnete Rolle sucht werden soll
	 * @return integer
	 */
	private static function getPermissionValue($key, $role_id)
	{
		$value = Registry::get('Db')->fetchAssoc('SELECT ru.permission FROM ' . DB_PRE . 'acl_roles AS r, ' . DB_PRE . 'acl_roles AS parent JOIN ' . DB_PRE . 'acl_rules AS ru ON(parent.id = ru.role_id) JOIN ' . DB_PRE . 'acl_privileges AS p ON(ru.privilege_id = p.id) WHERE r.id = ? AND p.key = ? AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1', array($role_id, $key));
		return isset($value['permission']) ? $value['permission'] : 0;
	}
	/**
	 * Gibt die Rollen-Berechtigungen aus
	 *
	 * @param array $roles
	 *	Array mit den IDs der Rollen
	 * @return boolean
	 */
	public static function getRules(array $roles)
	{
		$filename = 'acl_rules_' . implode(',', $roles);
		if (Cache::check($filename, 'acl') === false)
			self::setRulesCache($roles);

		return Cache::output($filename, 'acl');
	}
	/**
	 * Gibt aus ob dem Benutzer die jeweilige Rolle zugeordnet ist
	 *
	 * @param integer $role_id
	 *	ID der zu überprüfenden Rolle
	 * @return boolean
	 */
	public static function userHasRole($role_id)
	{
		return in_array($role_id, self::$userRoles);
	}
	/**
	 * Gibt aus, ob ein Benutzer die Berechtigung auf eine Privilegie besitzt
	 *
	 * @param string $key
	 *	Der Schlssel der Privilegie
	 * @return boolean
	 */
	public static function userHasPrivilege($module, $key)
	{
		$key = strtolower($key);
		if (isset(self::$privileges[$module][$key]))
			return self::$privileges[$module][$key]['access'];
		return false;
	}
	/**
	 * Gibt aus, ob ein Benutzer berichtigt ist, eine Ressource zu betreten
	 *
	 * @param string $resource
	 *	Pfad der Ressource im Stile einer ACP3 internen URI
	 * @return boolean
	 */
	public static function canAccessResource($resource)
	{
		if (isset(self::$resources[$resource])) {
			$module = substr($resource, 0, strpos($resource, '/'));
			$key = self::$resources[$resource]['key'];
			return self::userHasPrivilege($module, $key) === true || ACP3_CMS::$auth->isSuperUser() === true ? true : false;
		}
		return false;
	}
}