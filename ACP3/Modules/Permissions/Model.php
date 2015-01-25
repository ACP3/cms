<?php
namespace ACP3\Modules\Permissions;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Permissions
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'acl_roles';
    const TABLE_NAME_PRIVILEGES = 'acl_privileges';
    const TABLE_NAME_RESOURCES = 'acl_resources';
    const TABLE_NAME_RULES = 'acl_rules';
    const TABLE_NAME_USER_ROLES = 'acl_user_roles';

    /**
     * @param $id
     * @return bool
     */
    public function roleExists($id)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id', ['id' => $id]) > 0);
    }

    /**
     * @param $roleName
     * @param int $id
     * @return bool
     */
    public function roleExistsByName($roleName, $id = 0)
    {
        if ($id !== 0) {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id != ? AND name = ?', [(int) $id, $roleName]) == 1;
        } else {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE name = ?', [$roleName]) == 1;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function resourceExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_RESOURCES . ' WHERE id = :id', ['id' => $id]) > 0;
    }

    /**
     * @param $id
     * @return bool
     */
    public function privilegeExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES . ' WHERE id = :id', ['id' => $id]) > 0;
    }

    /**
     * @param $id
     * @return array
     */
    public function getRoleById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->db->fetchAll('SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS p, ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
    }

    /**
     * @param $userId
     * @return array
     */
    public function getRolesByUserId($userId)
    {
        return $this->db->fetchAll('SELECT r.* FROM ' . $this->db->getPrefix() . static::TABLE_NAME_USER_ROLES . ' AS ur JOIN ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC', [$userId], [\PDO::PARAM_INT]);
    }

    /**
     * @param array $roles
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllRulesByRoleIds(array $roles)
    {
        return $this->db->getConnection()->executeQuery(
            'SELECT ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM ' . $this->db->getPrefix() . static::TABLE_NAME_RULES . ' AS ru JOIN ' . $this->db->getPrefix() . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON (ru.module_id = m.id) JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES . ' AS p ON(ru.privilege_id = p.id) WHERE m.active = 1 AND ru.role_id IN(?)',
            [$roles],
            [\Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
        )->fetchAll();
    }

    /**
     * @param $id
     * @return array
     */
    public function getResourceById($id)
    {
        return $this->db->fetchAssoc('SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->db->getPrefix() . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->db->getPrefix() . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = r.module_id) WHERE r.id = ?', [$id]);
    }

    /**
     * @return array
     */
    public function getAllResources()
    {
        return $this->db->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->db->getPrefix() . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->db->getPrefix() . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(r.module_id = m.id) JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES . ' AS p ON(r.privilege_id = p.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC');
    }

    /**
     * @return array
     */
    public function getAllResourceIds()
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES);
    }

    /**
     * @return array
     */
    public function getAllPrivileges()
    {
        return $this->db->fetchAll('SELECT id, `key`, description FROM ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES . ' ORDER BY `key` ASC');
    }

    /**
     * @param $key
     * @param $roleId
     * @return array
     */
    public function getPermissionByKeyAndRoleId($key, $roleId)
    {
        return $this->db->fetchAssoc('SELECT ru.permission FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS r, ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS parent JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_RULES . ' AS ru ON(parent.id = ru.role_id) JOIN ' . $this->db->getPrefix() . static::TABLE_NAME_PRIVILEGES . ' AS p ON(ru.privilege_id = p.id) WHERE r.id = ? AND p.key = ? AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1', [$roleId, $key]);
    }
}
