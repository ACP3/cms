<?php

namespace ACP3\Modules\Errors;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'errors';
    const SCHEMA_VERSION = 32;

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
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "errors/403/", "errors/index/403/") WHERE uri LIKE "errors/403/%";' : '',
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "errors/404/", "errors/index/404/") WHERE uri LIKE "errors/404/%";' : '',
            )
        );
    }

}
