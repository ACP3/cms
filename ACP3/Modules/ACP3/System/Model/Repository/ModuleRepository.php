<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Model\Repository;

use ACP3\Core;

/**
 * Class ModuleRepository
 * @package ACP3\Modules\ACP3\System\Model\Repository
 */
class ModuleRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'modules';

    /**
     * @param string $moduleName
     *
     * @return mixed
     */
    public function getModuleId($moduleName)
    {
        return $this->db->fetchColumn('SELECT `id` FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$moduleName]);
    }

    /**
     * @param string $moduleName
     *
     * @return mixed
     */
    public function getModuleSchemaVersion($moduleName)
    {
        return $this->db->fetchColumn('SELECT `version` FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$moduleName]);
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public function moduleExists($moduleName)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$moduleName]) > 0;
    }

    /**
     * @param string $moduleName
     *
     * @return array
     */
    public function getInfoByModuleName($moduleName)
    {
        return $this->db->fetchAssoc('SELECT `id`, `version`, `active` FROM ' . $this->getTableName() . ' WHERE `name` = ?', [$moduleName]);
    }

    /**
     * @param int $moduleId
     *
     * @return mixed
     */
    public function getModuleNameById($moduleId)
    {
        return $this->db->fetchColumn('SELECT `name` FROM ' . $this->getTableName() . ' WHERE `id` = ?', [$moduleId]);
    }
}
