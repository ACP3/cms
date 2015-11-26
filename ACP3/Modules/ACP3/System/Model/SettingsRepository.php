<?php
namespace ACP3\Modules\ACP3\System\Model;

use ACP3\Core;

/**
 * Class SettingsRepository
 * @package ACP3\Modules\ACP3\System\Model
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