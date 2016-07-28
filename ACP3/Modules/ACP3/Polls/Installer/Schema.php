<?php

namespace ACP3\Modules\ACP3\Polls\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Polls\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'polls';

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
        return 37;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}polls` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `multiple` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}poll_answers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `text` VARCHAR(120) NOT NULL,
                `poll_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `foreign_poll_id` (`poll_id`),
                FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}poll_votes` (
                `poll_id` INT(10) UNSIGNED NOT NULL,
                `answer_id` INT(10) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED,
                `ip` VARCHAR(40) NOT NULL,
                `time` DATETIME NOT NULL,
                INDEX (`poll_id`),
                INDEX (`answer_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`answer_id`) REFERENCES `{pre}poll_answers` (`id`) ON DELETE CASCADE,
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
            "DROP TABLE `{pre}poll_votes`;",
            "DROP TABLE `{pre}poll_answers`;",
            "DROP TABLE `{pre}polls`;"
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function removeSettings()
    {
        return true;
    }
}
