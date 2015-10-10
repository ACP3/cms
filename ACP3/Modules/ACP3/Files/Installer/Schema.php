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
        return 38;
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
                `user_id` INT UNSIGNED,
                PRIMARY KEY (`id`),
                FULLTEXT KEY `index` (`title`, `text`),
                INDEX `foreign_category_id` (`category_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}files`;"
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
