<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Model\Repository;

use ACP3\Core;

/**
 * Class SettingsRepository
 * @package ACP3\Modules\ACP3\System\Model\Repository
 */
class SettingsRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'settings';

    /**
     * @return array
     */
    public function getAllModuleSettings()
    {
        return $this->db->fetchAll('SELECT m.name AS module_name, s.name, s.value FROM ' . $this->getTableName() . ' AS s JOIN ' . $this->getTableName(ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = s.module_id) ORDER BY s.module_id');
    }
}
