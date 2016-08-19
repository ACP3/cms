<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Model\Repository\ModuleRepository;

/**
 * Class ResourceRepository
 * @package ACP3\Modules\ACP3\Permissions\Model\Repository
 */
class ResourceRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'acl_resources';

    /**
     * @param int $resourceId
     *
     * @return array
     */
    public function getOneById($resourceId)
    {
        return $this->db->fetchAssoc(
            'SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->getTableName() . ' AS r JOIN ' . $this->getTableName(ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = r.module_id) WHERE r.id = ?',
            [$resourceId]
        );
    }

    /**
     * @return array
     */
    public function getAllResources()
    {
        return $this->db->fetchAll(
            'SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->getTableName() . ' AS r JOIN ' . $this->getTableName(ModuleRepository::TABLE_NAME) . ' AS m ON(r.module_id = m.id) JOIN ' . $this->getTableName(PrivilegeRepository::TABLE_NAME) . ' AS p ON(r.privilege_id = p.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC'
        );
    }
}
