<?php

namespace ACP3\Modules\ACP3\Search;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Search
 */
class Installer extends Modules\SchemaInstaller
{
    const MODULE_NAME = 'search';
    const SCHEMA_VERSION = 32;

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
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
            ],
            32 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "search/", "search/index/") WHERE `uri` LIKE "search/%";',
            ],
            33 => [
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "search/list/", "search/index/index/") WHERE `uri` LIKE "search/list/%";' : '',
            ]
        ];
    }
}
