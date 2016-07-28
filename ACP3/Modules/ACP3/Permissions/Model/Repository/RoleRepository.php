<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;

/**
 * Class RoleRepository
 * @package ACP3\Modules\ACP3\Permissions\Model\Repository
 */
class RoleRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'acl_roles';

    /**
     * @param int $roleId
     *
     * @return bool
     */
    public function roleExists($roleId)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id', ['id' => $roleId]) > 0);
    }

    /**
     * @param string $roleName
     * @param int    $roleId
     *
     * @return bool
     */
    public function roleExistsByName($roleName, $roleId = 0)
    {
        if ($roleId !== 0) {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND `name` = ?', [(int)$roleId, $roleName]) == 1;
        } else {
            return !empty($roleName) && $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$roleName]) == 1;
        }
    }

    /**
     * @param int $roleId
     *
     * @return array
     */
    public function getRoleById($roleId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$roleId]);
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->db->fetchAll('SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS `level`, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS p, ' . $this->getTableName() . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
    }

    /**
     * @param string $privilegeKey
     * @param int    $roleId
     *
     * @return array
     */
    public function getPermissionByKeyAndRoleId($privilegeKey, $roleId)
    {
        return $this->db->fetchAssoc(
            'SELECT ru.permission FROM ' . $this->getTableName() . ' AS r, ' . $this->getTableName() . ' AS parent JOIN ' . $this->getTableName(RuleRepository::TABLE_NAME) . ' AS ru ON(parent.id = ru.role_id) JOIN ' . $this->getTableName(PrivilegeRepository::TABLE_NAME) . ' AS p ON(ru.privilege_id = p.id) WHERE r.id = ? AND p.key = ? AND ru.permission != 2 AND parent.left_id < r.left_id AND parent.right_id > r.right_id ORDER BY parent.left_id DESC LIMIT 1',
            [$roleId, $privilegeKey]
        );
    }
}
