<?php
namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core;

/**
 * Class UserRolesRepository
 * @package ACP3\Modules\ACP3\Permissions\Model
 */
class UserRoleRepository extends Core\Model
{
    const TABLE_NAME = 'acl_user_roles';

    /**
     * @param int $userId
     *
     * @return array
     */
    public function getRolesByUserId($userId)
    {
        return $this->db->fetchAll('SELECT r.* FROM ' . $this->getTableName() . ' AS ur JOIN ' . $this->getTableName(RoleRepository::TABLE_NAME) . ' AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC', [$userId], [\PDO::PARAM_INT]);
    }
}