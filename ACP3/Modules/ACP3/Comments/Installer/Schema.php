<?php

namespace ACP3\Modules\ACP3\Comments\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Comments\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}comments` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` VARCHAR(40) NOT NULL,
                `date` DATETIME NOT NULL,
                `name` VARCHAR(20) NOT NULL,
                `user_id` INT(10) UNSIGNED,
                `message` TEXT NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `entry_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`module_id`, `entry_id`),
                INDEX (`module_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}comments`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'dateformat' => 'long',
            'emoticons' => 1,
        ];
    }

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
        return static::MODULE_NAME;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 36;
    }
}
