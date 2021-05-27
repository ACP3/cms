<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Connection;

class RuleRepository extends Core\Model\Repository\AbstractRepository
{
    public const TABLE_NAME = 'acl_rules';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllRulesByRoleIds(array $roleIds): array
    {
        return $this->db->getConnection()->executeQuery(
            'SELECT ru.id, ru.role_id, ru.privilege_id, ru.permission, ru.module_id, m.name AS module_name, p.key, p.description FROM ' . $this->getTableName() . ' AS ru JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON (ru.module_id = m.id) JOIN ' . $this->getTableName(PrivilegeRepository::TABLE_NAME) . " AS p ON(ru.privilege_id = p.id) JOIN {$this->getTableName(RoleRepository::TABLE_NAME)} AS ro ON(ro.id = ru.role_id) AND ro.id IN(?)",
            [$roleIds],
            [Connection::PARAM_INT_ARRAY]
        )->fetchAllAssociative();
    }
}
