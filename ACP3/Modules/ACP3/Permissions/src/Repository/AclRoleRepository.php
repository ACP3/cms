<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core;

class AclRoleRepository extends Core\NestedSet\Repository\NestedSetRepository implements Core\ACL\Repository\RoleRepositoryInterface
{
    public const TABLE_NAME = 'acl_roles';

    public function roleExists(int $roleId): bool
    {
        return (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `id` = :id', ['id' => $roleId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function roleExistsByName(string $roleName, int $roleId = 0): bool
    {
        if ($roleId !== 0) {
            return !empty($roleName) && (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id != ? AND `name` = ?', [$roleId, $roleName]) === 1;
        }

        return !empty($roleName) && (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$roleName]) === 1;
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllRoles(): array
    {
        return $this->db->fetchAll(
            'SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS `level`, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS p, ' . $this->getTableName() . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id, n.id, n.name, n.parent_id, n.right_id ORDER BY n.left_id'
        );
    }
}
