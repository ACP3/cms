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
	 * @return array
	 */
	public function getAllRoles($reset_cache = false)
	{
		global $db;

		$filename = 'acl_all_roles';
		if (!cache::check($filename) || $reset_cache === true) {
			$roles = $db->query('SELECT n.id, n.name, n.left_id, n.right_id, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM {pre}acl_roles AS p, {pre}acl_roles AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
			$c_roles = count($roles);

			for ($i = 0; $i < $c_roles; ++$i) {
				$roles[$i]['name'] = $db->escape($roles[$i]['name'], 3);

				// Bestimmen, ob die Seite die Erste und/oder Letzte eines Blocks/Knotens ist
				$first = $last = false;
				if ($i == 0 ||
					isset($roles[$i - 1]) &&
					($roles[$i - 1]['level'] < $roles[$i]['level'] ||
					$roles[$i]['level'] < $roles[$i - 1]['level'] ||
					$roles[$i]['level'] == $roles[$i - 1]['level']))
					$first = true;
				if ($i == $c_roles - 1 ||
					isset($roles[$i + 1]) &&
					($roles[$i]['level'] == 0 && $roles[$i + 1]['level'] == 0 ||
					$roles[$i]['level'] > $roles[$i + 1]['level']))
					$last = true;

				// Checken, ob für das aktuelle Element noch Nachfolger existieren
				if (!$last) {
					for ($j = $i + 1; $j < $c_roles; ++$j) {
						if ($roles[$i]['level'] == $roles[$j]['level']) {
							$found = true;
							break;
						}
					}
					if (!isset($found))
						$last = true;
					else
						unset($found);
				}

				$roles[$i]['first'] = $first;
				$roles[$i]['last'] = $last;
			}

			cache::create($filename, $roles);
		}

		return cache::output($filename);
	}
	/**
	 *
	 * @return array
	 */
	public function getAllPrivileges()
	{
		global $db;

		$privileges = $db->select('id, key, name', 'acl_privileges', 0, 'key ASC');
		$c_privileges = count($privileges);
		$return = array();

		for ($i = 0; $i < $c_privileges; ++$i) {
			$return[$privileges[$i]['key']] = array(
				'id' => $privileges[$i]['id'],
				'name' => $db->escape($privileges[$i]['name'], 3),
			);
		}
		return $return;
	}
	/**
	 *
	 * @param array $role
	 * @return boolean
	 */
	public function getRolePrivileges(array $role)
	{
		global $db;

		// Berechtigungen einlesen, auf die der Benutzer laut seinen Rollen Zugriff hat
		$roles = $db->query('SELECT rp.role_id, rp.privilege_id, rp.value, p.key, p.name FROM {pre}acl_role_privileges AS rp JOIN {pre}acl_privileges AS p ON(rp.privilege_id = p.id) WHERE rp.role_id IN(' . implode(',', $role) . ')');
		$c_roles = count($roles);
		$privileges = array();
		for ($i = 0; $i < $c_roles; ++$i) {
			$key = strtolower($roles[$i]['key']);
			$privileges[$key] = array(
				'id' => $roles[$i]['privilege_id'],
				'name' => $db->escape($roles[$i]['name'], 3),
				'value' => $roles[$i]['value'] === '1' || $roles[$i]['value'] === '2' ? true : false,
			);
		}
		return $privileges;
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
			return $this->privileges[$key]['value'] === true ? true : false;
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