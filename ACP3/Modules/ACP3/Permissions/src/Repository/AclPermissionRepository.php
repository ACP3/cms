<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\Repository\AclPermissionRepositoryInterface;
use ACP3\Core\Repository\AbstractRepository;
use Doctrine\DBAL\Connection;

class AclPermissionRepository extends AbstractRepository implements AclPermissionRepositoryInterface
{
    public const TABLE_NAME = 'acl_permission';

    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPermissionsByRoleIds(array $roleIds): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()} WHERE role_id IN(:roleIds)", ['roleIds' => $roleIds], ['roleIds' => Connection::PARAM_INT_ARRAY]);
    }

    /**
     * @param int[] $roleIds
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPermissionsByRoleIdsWithInheritance(array $roleIds): array
    {
        return $this->db->fetchAll("
        SELECT are.id \"resource_id\", tmp_permissions.permission \"permission\"
          FROM {$this->getTableName(AclResourceRepository::TABLE_NAME)} are
          LEFT JOIN (
    SELECT ap.*
    FROM {$this->getTableName()} ap WHERE ap.role_id IN(
        SELECT aro_p.id
        FROM {$this->getTableName(AclRoleRepository::TABLE_NAME)} aro_c,
             {$this->getTableName(AclRoleRepository::TABLE_NAME)} aro_p
        WHERE aro_c.left_id BETWEEN aro_p.left_id AND aro_p.right_id
          AND aro_c.id in (:roleIds)
        GROUP BY aro_p.id
        ORDER BY aro_p.left_id
    ) AND ap.permission != :inheritedValue
) tmp_permissions on (tmp_permissions.resource_id = are.id)
GROUP BY are.id, tmp_permissions.permission
ORDER BY are.id;
", ['roleIds' => $roleIds, 'inheritedValue' => PermissionEnum::INHERIT_ACCESS->value], ['roleIds' => Connection::PARAM_INT_ARRAY]);
    }
}
