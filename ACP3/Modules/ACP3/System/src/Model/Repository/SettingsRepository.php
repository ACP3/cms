<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\Repository\SettingsAwareRepositoryInterface;

class SettingsRepository extends AbstractRepository implements SettingsAwareRepositoryInterface
{
    public const TABLE_NAME = 'settings';

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllSettings()
    {
        return $this->db->fetchAll(
            "SELECT m.name AS module_name, s.name, s.value FROM {$this->getTableName()} AS s JOIN {$this->getTableName(ModulesRepository::TABLE_NAME)} AS m ON(m.id = s.module_id) ORDER BY s.module_id"
        );
    }
}
