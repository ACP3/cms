<?php

namespace ACP3\Modules\Search;

use ACP3\Core\Modules;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'search';
    const SCHEMA_VERSION = 32;

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

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
            ),
            32 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "search/", "search/index/") WHERE uri LIKE "search/%";',
            ),
            33 => array(
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "search/list/", "search/index/index/") WHERE uri LIKE "search/list/%";' : '',
            )
        );
    }

}
