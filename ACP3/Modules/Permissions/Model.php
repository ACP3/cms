<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Permissions;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'acl_roles';
    const TABLE_NAME_PRIVILEGES = 'acl_privileges';
    const TABLE_NAME_RESOURCES = 'acl_resources';
    const TABLE_NAME_RULES = 'acl_rules';
    const TABLE_NAME_USER_ROLES = 'acl_user_roles';

    public function roleExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function roleExistsByName($roleName, $id = '')
    {
        if (Core\Validate::isNumber($id) === true) {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id != ? AND name = ?', array($id, $roleName)) == 1 ? true : false;
        } else {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE name = ?', array($roleName)) == 1 ? true : false;
        }
    }

    public function resourceExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function getRoleById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getAllRoles()
    {
        return $this->db->fetchAll('SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->prefix . static::TABLE_NAME . ' AS p, ' . $this->prefix . static::TABLE_NAME . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
    }

    public function getRolesByUserId($userId)
    {
        return $this->db->fetchAll('SELECT r.* FROM ' . $this->prefix . static::TABLE_NAME_USER_ROLES . ' AS ur JOIN ' . $this->prefix . static::TABLE_NAME . ' AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC', array($userId), array(\PDO::PARAM_INT));
    }

    public function getAllRulesByRoleIds(array $roles)
    {
        return $this->db->executeQuery(
            'SELECT ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM ' . $this->prefix . static::TABLE_NAME_RULES . ' AS ru JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON (ru.module_id = m.id) JOIN ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' AS p ON(ru.privilege_id = p.id) WHERE m.active = 1 AND ru.role_id IN(?)',
            array($roles),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        )->fetchAll();
    }

    public function getResourceById($id)
    {
        return $this->db->fetchAssoc('SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = r.module_id) WHERE r.id = ?', array($id));
    }

    public function getAllResources()
    {
        return $this->db->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(r.module_id = m.id) JOIN ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' AS p ON(r.privilege_id = p.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC');
    }

    public function getAllResourceIds()
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_PRIVILEGES);
    }

    public function getAllPrivileges()
    {
        return $this->db->fetchAll('SELECT id, `key`, description FROM ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' ORDER BY `key` ASC');
    }

    public function getPermissionByKeyAndRoleId($key, $roleId)
    {
        return $this->db->fetchAssoc('SELECT ru.permission FROM ' . $this->prefix . static::TABLE_NAME . ' AS r, ' . $this->prefix . static::TABLE_NAME . ' AS parent JOIN ' . $this->prefix . static::TABLE_NAME_RULES . ' AS ru ON(parent.id = ru.role_id) JOIN ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' AS p ON(ru.privilege_id = p.id) WHERE r.id = ? AND p.key = ? AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1', array($roleId, $key));
    }

}
