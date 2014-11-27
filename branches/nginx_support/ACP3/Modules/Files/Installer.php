<?php

namespace ACP3\Modules\Files;

use ACP3\Core\Modules;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

/**
 * Class Installer
 * @package ACP3\Modules\Files
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'files';
    const SCHEMA_VERSION = 34;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}files` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `category_id` INT(10) UNSIGNED NOT NULL,
                `file` VARCHAR(120) NOT NULL,
                `size` VARCHAR(20) NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`), INDEX `foreign_category_id` (`category_id`)
            ) {engine} {charset};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}files`;",
            "DELETE FROM `{pre}categories` WHERE module_id = " . $this->getModuleId() . ";"
        ];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'comments' => 1,
            'dateformat' => 'long',
            'sidebar' => 5,
        ];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "ALTER TABLE `{pre}files` CHANGE `link_title` `title` VARCHAR(120) {charset} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"extensions/feeds\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "files/", "files/index/") WHERE uri LIKE "files/%";',
            ],
            34 => [
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "files/list/", "files/index/index/") WHERE uri LIKE "files/list/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "files/files/", "files/index/files/") WHERE uri LIKE "files/files/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "files/details/", "files/index/details/") WHERE uri LIKE "files/details/%";' : '',
            ]
        ];
    }
}
