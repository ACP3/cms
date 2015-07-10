<?php

namespace ACP3\Modules\ACP3\Gallery;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Gallery
 */
class Installer extends Modules\SchemaInstaller
{
    const MODULE_NAME = 'gallery';
    const SCHEMA_VERSION = 35;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}gallery` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}gallery_pictures` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pic` INT(10) UNSIGNED NOT NULL,
                `gallery_id` INT(10) UNSIGNED NOT NULL,
                `file` VARCHAR(120) NOT NULL,
                `description` TEXT NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_gallery_id` (`gallery_id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}gallery_pictures`;",
            "DROP TABLE `{pre}gallery`;"
        ];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'width' => 640,
            'height' => 480,
            'thumbwidth' => 160,
            'thumbheight' => 120,
            'maxwidth' => 2048,
            'maxheight' => 1536,
            'filesize' => 20971520,
            'overlay' => 1,
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
                "ALTER TABLE `{pre}gallery` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "gallery/", "gallery/index/") WHERE `uri` LIKE "gallery/%";',
            ],
            34 => [
                'UPDATE `{pre}acl_resources` SET `controller` = "pictures" WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_picture";',
                'UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, "_picture", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_picture";',
                'UPDATE `{pre}acl_resources` SET `controller` = "pictures" WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` = "order";',
            ],
            35 => [
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/list/", "gallery/index/index/") WHERE `uri` LIKE "gallery/list/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/pics/", "gallery/index/pics/") WHERE `uri` LIKE "gallery/pics/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/details/", "gallery/index/details/") WHERE `uri` LIKE "gallery/details/%";' : '',
            ]
        ];
    }
}
