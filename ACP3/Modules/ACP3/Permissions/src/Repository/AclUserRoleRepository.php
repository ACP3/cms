<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core;

class AclUserRoleRepository extends Core\Repository\AbstractRepository implements Core\ACL\Repository\UserRoleRepositoryInterface
{
    public const TABLE_NAME = 'acl_user_roles';

    public function getRolesByUserId(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT r.* FROM ' . $this->getTableName() . ' AS ur JOIN ' . $this->getTableName(AclRoleRepository::TABLE_NAME) . ' AS r ON(ur.role_id = r.id) WHERE ur.user_id = ? ORDER BY r.left_id DESC',
            [$userId],
            [\PDO::PARAM_INT]
        );
    }
}
