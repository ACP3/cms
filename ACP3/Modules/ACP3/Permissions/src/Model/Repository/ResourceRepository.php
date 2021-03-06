<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;

class ResourceRepository extends Core\Model\Repository\AbstractRepository
{
    public const TABLE_NAME = 'acl_resources';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int $resourceId): array
    {
        return $this->db->fetchAssoc(
            'SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->getTableName() . ' AS r JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = r.module_id) WHERE r.id = ?',
            [$resourceId]
        );
    }

    /**
     * Returns all the resources of the currently installed and activated modules.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllResources(): array
    {
        return $this->db->fetchAll('
            SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name
              FROM ' . $this->getTableName() . ' AS r
              JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(r.module_id = m.id)
              JOIN ' . $this->getTableName(PrivilegeRepository::TABLE_NAME) . ' AS p ON(r.privilege_id = p.id)
          ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC
        ');
    }
}
