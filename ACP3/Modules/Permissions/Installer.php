<?php

namespace ACP3\Modules\Permissions;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'permissions';
    const SCHEMA_VERSION = 31;

    public function renameModule()
    {
        return array(
            31 => "UPDATE `{pre}modules` SET name = 'permissions' WHERE name = 'access';"
        );
    }

    public function removeResources()
    {
        return true;
    }

    public function createTables()
    {
        return array();
    }

    public function removeTables()
    {
        return array();
    }

    public function settings()
    {
        return array();
    }

    public function removeSettings()
    {
        return true;
    }

    public function removeFromModulesTable()
    {
        return true;
    }

    public function schemaUpdates()
    {
        return array();
    }

}
