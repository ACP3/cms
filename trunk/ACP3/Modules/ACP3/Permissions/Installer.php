<?php

namespace ACP3\Modules\ACP3\Permissions;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Permissions
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'permissions';
    const SCHEMA_VERSION = 32;

    /**
     * @inheritdoc
     */
    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET `name` = 'permissions' WHERE `name` = 'access';"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeResources()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeFromModulesTable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            32 => [
                'UPDATE `{pre}acl_resources` SET `controller` = "resources" WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_resource%";',
                'UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, "_resources", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_resources";',
                'UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, "_resource", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_resource";',
            ]
        ];
    }
}
