<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;

class AclUserRolesRepository extends Core\Model\Repository\AbstractRepository implements Core\ACL\Model\Repository\AclUserRolesRepositoryInterface
{
    const TABLE_NAME = 'acl_user_roles';

    /**
     * @inheritdoc
     */
    public function getRolesByUserId($userId)
    {
        return $this->db->fetchAll(
            'SELECT r.* FROM ' . $this->getTableName() . ' AS ur JOIN ' . $this->getTableName(AclRolesRepository::TABLE_NAME) . ' AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC',
            [$userId],
            [\PDO::PARAM_INT]
        );
    }
}
