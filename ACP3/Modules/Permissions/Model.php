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

    public function getResourceById($id)
    {
        return $this->db->fetchAssoc('SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(m.id = r.module_id) WHERE r.id = ?', array($id));
    }

    public function getAllResources()
    {
        return $this->db->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->prefix . static::TABLE_NAME_RESOURCES . ' AS r JOIN ' . $this->prefix . \ACP3\Modules\System\Model::TABLE_NAME . ' AS m ON(r.module_id = m.id) JOIN ' . $this->prefix . static::TABLE_NAME_PRIVILEGES . ' AS p ON(r.privilege_id = p.id) ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC');
    }

}
