<?php

namespace ACP3\Modules\Permissions;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'permissions';
    const SCHEMA_VERSION = 32;

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
        return array(
            32 => array(
                'UPDATE `{pre}acl_resources` SET controller = "resources" WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resource%";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_resources", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resources";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_resource", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_resource";',
            )
        );
    }

}
