<?php

namespace ACP3\Modules\ACP3\Files\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Files
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
        return 'files';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 35;
    }

    /**
     * @return array
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
                `title` VARCHAR(255) NOT NULL,
                `text` TEXT NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`), INDEX `foreign_category_id` (`category_id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}files`;",
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
            'sidebar' => 5,
        ];
    }

}
