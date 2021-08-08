<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core\Repository\AbstractRepository;
use Doctrine\DBAL\Connection;

class AclPermissionRepository extends AbstractRepository
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
}
