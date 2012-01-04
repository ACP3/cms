<?php
/**
 * Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * ACL Klasse
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class acl
{
	/**
	 * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen
	 *
	 * @var array
	 */
	private $privileges = array();
	/**
	 * ID des eingeloggten Users
	 *
	 * @var array
	 */
	private $userId = 0;
	/**
	 * Array mit den dem Benutzer zugewiesenen Rollen
	 *
	 * @var array
	 */
	private $userRoles = array();
	/**
	 * Array mit allen registrierten Ressourcen
	 *
	 * @var array
	 */
	private $resources = array();

	/**
	 * Konstruktor - erzeugt die ACL für den jeweiligen User
	 *
	 * @param integer $user_id
	 */
	public function __construct($user_id = 0)
	{
		global $auth;

		$this->userId = $user_id === 0 ? $auth->getUserId() : (int) $user_id;
		$this->userRoles = $this->getUserRoles();
		$this->resources = $this->getResources();
		$this->privileges = $this->getRules($this->userRoles);
	}
	/**
	 * Erstellt den Cache für alle existierenden Ressourcen
	 *
	 * @return boolean
	 */
	public function setResourcesCache()
	{
		global $db;

		$resources = $db->query('SELECT r.id AS resource_id, r.module_id, m.name AS module, r.page, r.params, r.privilege_id, p.key FROM {pre}acl_resources AS r JOIN {pre}acl_privileges AS p ON(r.privilege_id = p.id) JOIN {pre}modules AS m ON(r.module_id = m.id) ORDER BY r.module_id ASC, r.page ASC');
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
		return cache::create('acl_resources', $data, 'acl');
	}
	/**
	 * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
	 *
	 * @return array
	 */
	public function getResources()
	{
		if (!cache::check('acl_resources', 'acl'))
			$this->setResourcesCache();

		return cache::output('acl_resources', 'acl');
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
	public function getUserRoles($user_id = 0, $mode = 1)
	{
		global $db;

		$user_id = $user_id === 0 ? $this->userId : $user_id;
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
	public function setRolesCache()
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
					if ($roles[$j]['parent_id'] == $roles[$i]['parent_id']) {
						$first = false;
						break;
					}
				}
			}
			
			for ($j = $i + 1; $j < $c_roles; ++$j) {
				if ($roles[$i]['parent_id'] == $roles[$j]['parent_id']) {
					$last = false;
					break;
				}
			}

			$roles[$i]['first'] = $first;
			$roles[$i]['last'] = $last;
		}

		return cache::create('acl_all_roles', $roles, 'acl');
	}
	/**
	 * Setzt den Cache für die einzelnen Berechtigungen einer Rolle
	 *
	 * @param array $roles
	 *	Array mit den IDs der zu cachenden Rollen
	 * @return boolean
	 */
	public function setRulesCache(array $roles)
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
				'access' => $rules[$i]['permission'] == 1 || ($rules[$i]['permission'] == 2 && $this->getPermissionValue($key, $rules[$i]['role_id']) == 1) ? true : false,
			);
		}

		return cache::create('acl_rules_' . implode(',', $roles), $privileges, 'acl');
	}
	/**
	 * Gibt alle existieren Rollen aus
	 *
	 * @return array
	 */
	public function getAllRoles()
	{
		if (!cache::check('acl_all_roles', 'acl'))
			$this->setRolesCache();

		return cache::output('acl_all_roles', 'acl');
	}
	/**
	 * Gibt alle existierenden Privilegien/Berechtigungen aus
	 *
	 * @return array
	 */
	public function getAllPrivileges()
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
	private function getPermissionValue($key, $role_id)
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
	public function getRules(array $roles)
	{
		$filename = 'acl_rules_' . implode(',', $roles);
		if (!cache::check($filename, 'acl'))
			$this->setRulesCache($roles);

		return cache::output($filename, 'acl');
	}
	/**
	 * Gibt aus ob dem Benutzer die jeweilige Rolle zugeordnet ist
	 *
	 * @param integer $role_id
	 *	ID der zu überprüfenden Rolle
	 * @return boolean
	 */
	public function userHasRole($role_id)
	{
		return in_array($role_id, $this->userRoles);
	}
	/**
	 * Gibt aus, ob ein Benutzer die Berechtigung auf eine Privilegie besitzt
	 *
	 * @param string $key
	 *	Der Schlssel der Privilegie
	 * @return boolean
	 */
	public function userHasPrivilege($module, $key)
	{
		$key = strtolower($key);
		if (isset($this->privileges[$module][$key]))
			return $this->privileges[$module][$key]['access'];
		return false;
	}
	/**
	 * Gibt aus, ob ein Benutzer berichtigt ist, eine Resource zu betreten
	 *
	 * @param string $resource
	 *	Pfad der Ressource im Stile einer ACP3 internen URI
	 * @return boolean
	 */
	public function canAccessResource($resource)
	{
		if (isset($this->resources[$resource]))
			return $this->userHasPrivilege(substr($resource, 0, strpos($resource, '/')), $this->resources[$resource]['key']);
		return false;
	}
}