<?php

namespace ACP3\Modules\ACP3\Polls\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Polls\Installer
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
        return 'polls';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 34;
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
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}poll_answers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `text` VARCHAR(120) NOT NULL,
                `poll_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}poll_votes` (
                `poll_id` INT(10) UNSIGNED NOT NULL,
                `answer_id` INT(10) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                `ip` VARCHAR(40) NOT NULL,
                `time` DATETIME NOT NULL,
                INDEX (`poll_id`, `answer_id`, `user_id`)
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
