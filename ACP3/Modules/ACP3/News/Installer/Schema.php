<?php

namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\News\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
     */
    public function specialResources()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'news';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 36;
    }

    /**
     * @return array
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
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}news`;",
            "DELETE FROM `{pre}categories` WHERE `module_id` = '{moduleId}';"
        ];
    }

    /**
     * @return array
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
}
