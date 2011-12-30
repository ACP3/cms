<?php
/**
 * Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
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
	public function __construct($user_id = '')
	{
		global $auth;

		$this->userId = $user_id === '' ? $auth->getUserId() : (int) $user_id;
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
	 * @return array
	 */
	public function getUserRoles()
	{
		global $db;

		$user_roles = $db->query('SELECT ur.role_id FROM {pre}acl_user_roles AS ur JOIN {pre}acl_roles AS r ON(ur.role_id = r.id) WHERE ur.user_id = \'' . $this->userId . '\' ORDER BY r.left_id DESC');
		$c_user_roles = count($user_roles);
		$roles = array();

		for ($i = 0; $i < $c_user_roles; ++$i) {
			$roles[] = $user_roles[$i]['role_id'];
		}
		return $roles;
	}
	/**
	 *
	 * @return array
	 */
	public function getAllRoles()
	{
		global $db;

		$roles = $db->select('id, name', 'acl_roles', 0, 'left_id ASC');
		$c_roles = count($roles);

		for ($i = 0; $i < $c_roles; ++$i) {
			$roles[$i]['name'] = $db->escape($roles[$i]['name'], 3);
		}
		return $roles;
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