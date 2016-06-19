<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;

/**
 * Class UserRoleRepository
 * @package ACP3\Modules\ACP3\Permissions\Model\Repository
 */
class UserRoleRepository extends Core\Model\AbstractRepository
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
