<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;
use ACP3\Core\ACL\Model\Repository\RoleRepositoryInterface;

class RoleRepository extends Core\NestedSet\Model\Repository\NestedSetRepository implements RoleRepositoryInterface
{
    const TABLE_NAME = 'acl_roles';

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

    public function getAllRoles(): array
    {
        return $this->db->fetchAll(
            'SELECT n.id, n.name, n.parent_id, n.left_id, n.right_id, COUNT(*)-1 AS `level`, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->getTableName() . ' AS p, ' . $this->getTableName() . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id, n.id, n.name, n.parent_id, n.right_id ORDER BY n.left_id'
        );
    }

    public function getPermissionByKeyAndRoleId(string $privilegeKey, int $moduleId, int $roleId): int
    {
        $query = <<<SQL
SELECT ru.permission
FROM {$this->getTableName()} AS r,
     {$this->getTableName()} AS parent
JOIN {$this->getTableName(RuleRepository::TABLE_NAME)} AS ru ON(parent.id = ru.role_id)
JOIN {$this->getTableName(PrivilegeRepository::TABLE_NAME)} AS p ON(ru.privilege_id = p.id)
WHERE r.id = :roleId AND p.key = :privilege AND ru.permission != :permission AND ru.module_id = :moduleId AND parent.left_id < r.left_id AND parent.right_id > r.right_id
ORDER BY parent.left_id DESC
LIMIT 1;
SQL;

        return (int) $this->db->fetchColumn(
            $query,
            ['roleId' => $roleId, 'privilege' => $privilegeKey, 'moduleId' => $moduleId, 'permission' => 2]
        );
    }
}
