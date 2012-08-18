<?php
/**
 * Access Control List
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * ACL Klasse
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_ACL
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
		global $db;

		$resources = $db->query('SELECT r.id AS resource_id, r.module_id, m.name AS module, r.page, r.params, r.privilege_id, p.key FROM {pre}acl_resources AS r JOIN {pre}acl_privileges AS p ON(r.privilege_id = p.id) JOIN {pre}modules AS m ON(r.module_id = m.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.page ASC');
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
		return ACP3_Cache::create('acl_resources', $data, 'acl');
	}
	/**
	 * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
	 *
	 * @return array
	 */
	public static function getResources()
	{
		if (ACP3_Cache::check('acl_resources', 'acl') === false)
			self::setResourcesCache();

		return ACP3_Cache::output('acl_resources', 'acl');
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
		global $db;

		$user_id = $user_id === 0 ? self::$userId : $user_id;
		$field = $mode === 2 ? 'r.name' : 'r.id';
		$key = substr($field, 2);
		$user_roles = $db->query('SELECT ' . $field . ' FROM {pre}acl_user_roles AS ur JOIN {pre}acl_roles AS r ON(ur.role_id = r.id) WHERE ur.user_id = \'' . $user_id . '\' ORDER BY r.left_id DESC');
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
		global $db;

		$roles = $db->query('SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM {pre}acl_roles AS p, {pre}acl_roles AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
		$c_roles = count($roles);

		for ($i = 0; $i < $c_roles; ++$i) {
			$roles[$i]['name'] = $db->escape($roles[$i]['name'], 3);

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

		return ACP3_Cache::create('acl_all_roles', $roles, 'acl');
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
		global $db;

		// Berechtigungen einlesen, auf die der Benutzer laut seinen Rollen Zugriff hat
		$rules = $db->query('SELECT ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM {pre}acl_rules AS ru JOIN {pre}modules AS m ON (ru.module_id = m.id) JOIN {pre}acl_privileges AS p ON(ru.privilege_id = p.id) WHERE ru.role_id IN(' . implode(',', $roles) . ')');
		$c_rules = count($rules);
		$privileges = array();
		for ($i = 0; $i < $c_rules; ++$i) {
			$key = strtolower($rules[$i]['key']);
			$privileges[$rules[$i]['module_name']][$key] = array(
				'id' => $rules[$i]['privilege_id'],
				'description' => $db->escape($rules[$i]['description'], 3),
				'permission' => $rules[$i]['permission'],
				'access' => $rules[$i]['permission'] == 1 || ($rules[$i]['permission'] == 2 && self::getPermissionValue($key, $rules[$i]['role_id']) == 1) ? true : false,
			);
		}

		return ACP3_Cache::create('acl_rules_' . implode(',', $roles), $privileges, 'acl');
	}
	/**
	 * Gibt alle existieren Rollen aus
	 *
	 * @return array
	 */
	public static function getAllRoles()
	{
		if (ACP3_Cache::check('acl_all_roles', 'acl') === false)
			self::setRolesCache();

		return ACP3_Cache::output('acl_all_roles', 'acl');
	}
	/**
	 * Gibt alle existierenden Privilegien/Berechtigungen aus
	 *
	 * @return array
	 */
	public static function getAllPrivileges()
	{
		global $db;

		$privileges = $db->select('id, `key`, description', 'acl_privileges', 0, '`key` ASC');
		$c_privileges = count($privileges);

		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['description'] = $db->escape($privileges[$i]['description'], 3);
		}
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
		global $db;

		$value = $db->query('SELECT ru.permission FROM {pre}acl_roles AS r, {pre}acl_roles AS parent JOIN {pre}acl_rules AS ru ON(parent.id = ru.role_id) JOIN {pre}acl_privileges AS p ON(ru.privilege_id = p.id) WHERE r.id = \'' . $db->escape($role_id) . '\' AND p.key = \'' . $db->escape($key) . '\' AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1');
		return isset($value[0]['permission']) ? $value[0]['permission'] : 0;
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
		if (ACP3_Cache::check($filename, 'acl') === false)
			self::setRulesCache($roles);

		return ACP3_Cache::output($filename, 'acl');
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
			global $auth;

			return self::userHasPrivilege(substr($resource, 0, strpos($resource, '/')), self::$resources[$resource]['key']) === true || $auth->isSuperUser() === true ? true : false;
		}
		return false;
	}
	/**
	 * Setzt die Ressourcen-Tabelle auf die Standardwerte zurück
	 */
	public static function resetResources()
	{
		global $db;

		$db->query('TRUNCATE TABLE {pre}acl_resources', 0);

		$special_resources = array(
			'comments' => array(
				'create' => 2,
			),
			'gallery' => array(
				'acp_add_picture' => 4,
			),
			'guestbook' => array(
				'create' => 2,
			),
			'newsletter' => array(
				'acp_activate' => 3,
				'acp_sent' => 4,
			),
			'system' => array(
				'acp_configuration' => 7,
				'acp_designs' => 7,
				'acp_extensions' => 7,
				'acp_languages' => 7,
				'acp_maintenance' => 7,
				'acp_modules' => 7,
				'acp_sql_export' => 7,
				'acp_sql_import' => 7,
				'acp_update_check' => 3,
			),
		);

		// Moduldaten in die ACL schreiben
		$modules = scandir(MODULES_DIR);
		foreach ($modules as $row) {
			if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml') === true) {
				$module = scandir(MODULES_DIR . $row . '/');

				$mod_id = $db->select('id', 'modules', 'name = \'' . $db->escape($row) . '\'');
				if (isset($mod_id[0]['id'])) {
					$mod_id = $mod_id[0]['id'];
				} else {
					$db->insert('modules', array('id' => '', 'name' => $row, 'active' => 1));
					$mod_id = $db->link->lastInsertId();
				}

				if (is_file(MODULES_DIR . $row . '/extensions/search.php') === true)
					$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
				if (is_file(MODULES_DIR . $row . '/extensions/feeds.php') === true)
					$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));

				foreach ($module as $file) {
					if ($file !== '.' && $file !== '..' && is_file(MODULES_DIR . $row . '/' . $file) === true && strpos($file, '.php') !== false) {
						$file = substr($file, 0, -4);
						if (isset($special_resources[$row][$file])) {
							$privilege_id = $special_resources[$row][$file];
						} else {
							$privilege_id = 1;
							if (strpos($file, 'acp_') === 0)
								$privilege_id = 3;
							if (strpos($file, 'acp_create') === 0 || strpos($file, 'acp_order') === 0)
								$privilege_id = 4;
							elseif (strpos($file, 'acp_edit') === 0)
								$privilege_id = 5;
							elseif (strpos($file, 'acp_delete') === 0)
								$privilege_id = 6;
							elseif (strpos($file, 'acp_settings') === 0)
								$privilege_id = 7;
						}
						$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => $file, 'params' => '', 'privilege_id' => $privilege_id));
					}
				}
			}
		}
	}
}