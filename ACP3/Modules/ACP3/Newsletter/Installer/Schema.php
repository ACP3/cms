<?php

namespace ACP3\Modules\ACP3\Newsletter\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Newsletter\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'Admin' => [
                'Index' => [
                    'send' => 4
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'newsletter';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 47;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}newsletter_accounts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mail` VARCHAR(255) NOT NULL,
                `salutation` TINYINT(1) NOT NULL,
                `first_name` VARCHAR(255) NOT NULL,
                `last_name` VARCHAR(255) NOT NULL,
                `hash` VARCHAR(128) NOT NULL,
                `status` TINYINT(1) NOT NULL,
                PRIMARY KEY (`id`), INDEX(`mail`), INDEX(`hash`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}newsletter_account_history` (
                `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                `date` DATETIME NOT NULL,
                `action` TINYINT(1) NOT NULL,
                INDEX (`newsletter_account_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}newsletter_queue` (
                `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                `newsletter_id` INT(10) UNSIGNED NOT NULL,
                INDEX (`newsletter_account_id`), INDEX (`newsletter_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}newsletters` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `html` TINYINT(1) NOT NULL,
                `status` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}newsletter_accounts`;",
            "DROP TABLE `{pre}newsletter_queue`;",
            "DROP TABLE `{pre}newsletters`;"
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'mail' => '',
            'mailsig' => '',
            'html' => 1
        ];
    }
}
