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
	 *
	 */
	public function __construct($user_id = 0)
	{
		global $auth;

		$this->userId = $user_id === 0 ? $auth->getUserId() : (int) $user_id;
		$this->userRoles = $this->getUserRoles();
		$this->resources = $this->getResources();
		$this->privileges = $this->getRolePrivileges($this->userRoles);
	}
	/**
	 *
	 * @return array
	 */
	public function getResources()
	{
		global $db;

		$resources = $db->query('SELECT r.path, r.privilege_id, p.key FROM {pre}acl_resources AS r JOIN {pre}acl_privileges AS p ON(r.privilege_id = p.id) ORDER BY r.path ASC');
		$c_resources = count($resources);
		$return = array();

		for ($i = 0; $i < $c_resources; ++$i) {
			$resources[$i]['path'] = $db->escape($resources[$i]['path'], 3);
			$return[$resources[$i]['path']] = array(
				'id' => $resources[$i]['privilege_id'],
				'key' => $resources[$i]['key'],
			);
		}
		return $return;
	}
	/**
	 *
	 * @param integer $user_id
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
	 *
	 * @param array $roles
	 * @return boolean
	 */
	public function setRolePrivilegesCache(array $roles)
	{
		global $db;

		// Berechtigungen einlesen, auf die der Benutzer laut seinen Rollen Zugriff hat
		$role_privs = $db->query('SELECT rp.role_id, rp.privilege_id, rp.value, p.key, p.name FROM {pre}acl_role_privileges AS rp JOIN {pre}acl_privileges AS p ON(rp.privilege_id = p.id) WHERE rp.role_id IN(' . implode(',', $roles) . ')');
		$c_role_privs = count($role_privs);
		$privileges = array();
		for ($i = 0; $i < $c_role_privs; ++$i) {
			$key = strtolower($role_privs[$i]['key']);
			$privileges[$key] = array(
				'id' => $role_privs[$i]['privilege_id'],
				'name' => $db->escape($role_privs[$i]['name'], 3),
				'value' => $role_privs[$i]['value'],
				'access' => $role_privs[$i]['value'] == 1 || ($role_privs[$i]['value'] == 2 && $this->getRolePrivilegeValue($key, $role_privs[$i]['role_id']) == 1) ? true : false,
			);
		}

		return cache::create('acl_role_privileges_' . implode(',', $roles), $privileges, 'acl');
	}
	/**
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
	 *
	 * @return array
	 */
	public function getAllPrivileges()
	{
		global $db;

		$privileges = $db->select('id, `key`, name', 'acl_privileges', 0, '`key` ASC');
		$c_privileges = count($privileges);

		for ($i = 0; $i < $c_privileges; ++$i) {
			$privileges[$i]['name'] = $db->escape($privileges[$i]['name'], 3);
		}
		return $privileges;
	}
	/**
	 *
	 * @param string $key
	 * @param integer $role_id
	 * @return integer
	 */
	public function getRolePrivilegeValue($key, $role_id)
	{
		global $db;

		$value = $db->query('SELECT rp.value FROM {pre}acl_roles AS r, {pre}acl_roles AS parent JOIN {pre}acl_role_privileges AS rp ON(parent.id = rp.role_id) JOIN {pre}acl_privileges AS p ON(rp.privilege_id = p.id) WHERE r.id = \'' . $db->escape($role_id) . '\' AND p.key = \'' . $db->escape($key) . '\' AND rp.value != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1');
		return isset($value[0]['value']) ? $value[0]['value'] : 0;
	}
	/**
	 *
	 * @param array $roles
	 * @return boolean
	 */
	public function getRolePrivileges(array $roles)
	{
		$filename = 'acl_role_privileges_' . implode(',', $roles);
		if (!cache::check($filename, 'acl'))
			$this->setRolePrivilegesCache($roles);

		return cache::output($filename, 'acl');
	}
	/**
	 *
	 * @param integer $role_id
	 * @return boolean
	 */
	public function userHasRole($role_id)
	{
		return in_array($role_id, $this->userRoles);
	}
	/**
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function userHasPrivilege($key)
	{
		$key = strtolower($key);
		if (isset($this->privileges[$key]))
			return $this->privileges[$key]['access'];
		return false;
	}
	/**
	 *
	 * @param string $resource
	 * @return boolean
	 */
	public function canAccessResource($resource)
	{
		if (isset($this->resources[$resource]))
			return $this->userHasPrivilege($this->resources[$resource]['key']);
		return false;
	}
}