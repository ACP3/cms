<?php

namespace ACP3\Modules\Permissions;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Permissions
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'permissions';
    const SCHEMA_VERSION = 32;

    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET name = 'permissions' WHERE name = 'access';"
        ];
    }

    public function removeResources()
    {
        return true;
    }

    public function createTables()
    {
        return [];
    }

    public function removeTables()
    {
        return [];
    }

    public function settings()
    {
        return [];
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
        return [
            32 => [
                'UPDATE `{pre}acl_resources` SET controller = "resources" WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resource%";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_resources", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resources";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_resource", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resource";',
            ]
        ];
    }

}
