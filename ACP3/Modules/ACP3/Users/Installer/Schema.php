<?php

namespace ACP3\Modules\ACP3\Users\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Users\Installer
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
        return 'users';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 42;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}users` (
                `id` INT(10) UNSIGNED AUTO_INCREMENT,
                `super_user` TINYINT(1) UNSIGNED NOT NULL,
                `nickname` VARCHAR(30) NOT NULL,
                `pwd` VARCHAR(128) NOT NULL,
                `pwd_salt` VARCHAR(16) NOT NULL,
                `remember_me_token` VARCHAR(128) NOT NULL,
                `login_errors` TINYINT(1) UNSIGNED NOT NULL,
                `realname` VARCHAR(80) NOT NULL,
                `gender` TINYINT(1) NOT NULL,
                `birthday` VARCHAR(10) NOT NULL,
                `birthday_display` TINYINT(1) UNSIGNED NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `mail_display` TINYINT(1) UNSIGNED NOT NULL,
                `website` VARCHAR(120) NOT NULL,
                `icq` VARCHAR(11) NOT NULL,
                `skype` VARCHAR(30) NOT NULL,
                `street` VARCHAR(120) NOT NULL,
                `house_number` VARCHAR(5) NOT NULL,
                `zip` VARCHAR(6) NOT NULL,
                `city` VARCHAR(120) NOT NULL,
                `address_display` TINYINT(1) UNSIGNED NOT NULL,
                `country` CHAR(2) NOT NULL,
                `country_display` TINYINT(1) UNSIGNED NOT NULL,
                `date_format_long` VARCHAR(30) NOT NULL,
                `date_format_short` VARCHAR(30) NOT NULL,
                `time_zone` VARCHAR(100) NOT NULL,
                `language` VARCHAR(10) NOT NULL,
                `entries` TINYINT(2) UNSIGNED NOT NULL,
                `draft` TEXT NOT NULL,
                `registration_date` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'enable_registration' => 1,
            'entries_override' => 1,
            'language_override' => 1,
            'mail' => ''
        ];
    }
}
