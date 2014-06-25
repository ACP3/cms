<?php
namespace ACP3\Modules\System;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'modules';
    const TABLE_NAME_SETTINGS = 'settings';
    const TABLE_NAME_SEO = 'seo';

    public function getSchemaTables()
    {
        return $this->db->fetchAll('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE = ? AND TABLE_SCHEMA = ?', array('BASE TABLE', CONFIG_DB_NAME));
    }

    public function getModuleId($moduleName)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE name = ?', array($moduleName));
    }

    public function getSettingsByModuleName($moduleName)
    {
        return $this->db->fetchAll('SELECT s.name, s.value FROM ' . $this->prefix . static::TABLE_NAME_SETTINGS . ' AS s JOIN ' . $this->prefix . static::TABLE_NAME . ' AS m ON(m.id = s.module_id) WHERE m.name = ?', array($moduleName));
    }

    public function getModuleSchemaVersion($moduleName)
    {
        return $this->db->fetchColumn('SELECT version FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE name = ?', array($moduleName));
    }

    public function uriAliasExists($path)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_SEO . ' WHERE uri = ?', array($path)) > 0;
    }

    public function getAllUriAliases()
    {
        return $this->db->fetchAll('SELECT uri, alias FROM ' . $this->prefix . static::TABLE_NAME_SEO . ' WHERE alias != ""');
    }

}
