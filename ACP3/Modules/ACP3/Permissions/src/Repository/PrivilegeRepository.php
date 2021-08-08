<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core;
use ACP3\Core\ACL\Repository\PrivilegeRepositoryInterface;

class PrivilegeRepository extends Core\Repository\AbstractRepository implements PrivilegeRepositoryInterface
{
    public const TABLE_NAME = 'acl_privileges';

    /**
     * @param int $privilegeId
     *
     * @return bool
     */
    public function privilegeExists($privilegeId)
    {
        return (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id', ['id' => $privilegeId]) > 0;
    }

    public function getAllPrivilegeIds()
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->getTableName());
    }

    /**
     * @return array
     */
    public function getAllPrivileges()
    {
        return $this->db->fetchAll('SELECT id, `key`, description FROM ' . $this->getTableName() . ' ORDER BY `key` ASC');
    }
}
