<?php

namespace ACP3\Modules\Errors;

use ACP3\Core\Modules;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'errors';
    const SCHEMA_VERSION = 34;

    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    public function __construct(
        \Doctrine\DBAL\Connection $db,
        System\Model $systemModel,
        Permissions\Model $permissionsModel,
        Modules $modules
    )
    {
        parent::__construct($db, $systemModel, $permissionsModel);

        $this->modules = $modules;
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
            31 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "errors/", "errors/index/") WHERE uri LIKE "errors/%";',
            ),
            32 => array(
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "errors/403/", "errors/index/403/") WHERE uri LIKE "errors/403/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "errors/404/", "errors/index/404/") WHERE uri LIKE "errors/404/%";' : '',
            ),
            33 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'frontend', 'index', '500', '', 1);",
                "UPDATE `{pre}acl_resources` SET page = '401' WHERE module_id = " . $this->getModuleId() . " AND area = 'frontend' AND controller = 'index' AND page = '403';"
            ),
            34 => array(
                "UPDATE `{pre}acl_resources` SET page = '403' WHERE module_id = " . $this->getModuleId() . " AND area = 'frontend' AND controller = 'index' AND page = '401';"
            )
        );
    }

}
