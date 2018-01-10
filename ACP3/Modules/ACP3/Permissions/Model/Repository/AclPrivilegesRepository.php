<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;

class AclPrivilegesRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'acl_privileges';

    /**
     * @param int $privilegeId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function privilegeExists(int $privilegeId)
    {
        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id',
                ['id' => $privilegeId]
        ) > 0;
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllPrivilegeIds()
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->getTableName());
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllPrivileges()
    {
        return $this->db->fetchAll('SELECT id, `key`, description FROM ' . $this->getTableName() . ' ORDER BY `key` ASC');
    }
}
