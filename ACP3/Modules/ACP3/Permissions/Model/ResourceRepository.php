<?php
namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core;

/**
 * Class ResourceRepository
 * @package ACP3\Modules\ACP3\Permissions\Model
 */
class ResourceRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'acl_resources';

    /**
     * @param int $resourceId
     *
     * @return array
     */
    public function getResourceById($resourceId)
    {
        return $this->db->fetchAssoc('SELECT r.page, r.area, r.controller, r.privilege_id, m.name AS module_name FROM ' . $this->getTableName() . ' AS r JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = r.module_id) WHERE r.id = ?', [$resourceId]);
    }

    /**
     * @return array
     */
    public function getAllResources()
    {
        return $this->db->fetchAll('SELECT m.id AS module_id, m.name AS module_name, r.id AS resource_id, r.page, r.area, r.controller, r.privilege_id, p.key AS privilege_name FROM ' . $this->getTableName() . ' AS r JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(r.module_id = m.id) JOIN ' . $this->getTableName(PrivilegeRepository::TABLE_NAME) . ' AS p ON(r.privilege_id = p.id) WHERE m.active = 1 ORDER BY r.module_id ASC, r.area ASC, r.controller ASC, r.page ASC');
    }
}
