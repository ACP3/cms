<?php

namespace ACP3\Modules\Gallery;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'gallery';
    const SCHEMA_VERSION = 35;

    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}gallery` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}gallery_pictures` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pic` INT(10) UNSIGNED NOT NULL,
                `gallery_id` INT(10) UNSIGNED NOT NULL,
                `file` VARCHAR(120) NOT NULL,
                `description` TEXT NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_gallery_id` (`gallery_id`)
            ) {engine} {charset};"
        );
    }

    public function removeTables()
    {
        return array(
            "DROP TABLE `{pre}gallery_pictures`;",
            "DROP TABLE `{pre}gallery`;"
        );
    }

    public function settings()
    {
        return array(
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
        );
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "ALTER TABLE `{pre}gallery` CHANGE `name` `title` VARCHAR(120) {charset} NOT NULL;",
            ),
            32 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ),
            33 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "gallery/", "gallery/index/") WHERE uri LIKE "gallery/%";',
            ),
            34 => array(
                'UPDATE `{pre}acl_resources` SET controller = "pictures" WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_picture";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_picture", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_picture";',
                'UPDATE `{pre}acl_resources` SET controller = "pictures" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "order";',
            ),
            35 => array(
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "gallery/list/", "gallery/index/index/") WHERE uri LIKE "gallery/list/%";' : '',
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "gallery/pics/", "gallery/index/pics/") WHERE uri LIKE "gallery/pics/%";' : '',
                Modules::isInstalled('menus') || Modules::isInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "gallery/details/", "gallery/index/details/") WHERE uri LIKE "gallery/details/%";' : '',
            )
        );
    }

}