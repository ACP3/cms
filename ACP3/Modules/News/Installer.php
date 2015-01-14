<?php

namespace ACP3\Modules\News;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\News
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'news';
    const SCHEMA_VERSION = 34;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}news` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `readmore` TINYINT(1) UNSIGNED NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                `category_id` INT(10) UNSIGNED NOT NULL,
                `uri` VARCHAR(120) NOT NULL,
                `target` TINYINT(1) UNSIGNED NOT NULL,
                `link_title` VARCHAR(120) NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`,`text`), INDEX `foreign_category_id` (`category_id`)
            ) {engine} {charset};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}news`;",
            "DELETE FROM `{pre}categories` WHERE `module_id` = " . $this->getModuleId() . ";"
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
            'readmore' => 1,
            'readmore_chars' => 350,
            'sidebar' => 5,
            'category_in_breadcrumb' => 1
        ];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "ALTER TABLE `{pre}news` CHANGE `headline` `title` VARCHAR(120) {charset} NOT NULL",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"extensions/feeds\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "news/", "news/index/") WHERE `uri` LIKE "news/%";',
            ],
            34 => [
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "news/list/", "news/index/index/") WHERE `uri` LIKE "news/list/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "news/details/", "news/index/details/") WHERE `uri` LIKE "news/details/%";' : '',
            ]
        ];
    }
}
