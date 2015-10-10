<?php

namespace ACP3\Modules\ACP3\Articles\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Articles
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'articles';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 37;
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [];
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}articles` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}articles`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }
}
