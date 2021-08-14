<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    public const MODULE_NAME = 'newsletter';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'admin' => [
                'accounts' => [
                    'activate' => PrivilegeEnum::ADMIN_VIEW,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                ],
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'send' => PrivilegeEnum::ADMIN_CREATE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'archive' => [
                    'details' => PrivilegeEnum::FRONTEND_VIEW,
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
                'index' => [
                    'activate' => PrivilegeEnum::FRONTEND_VIEW,
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'unsubscribe' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'CREATE TABLE `{pre}newsletters` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `html` TINYINT(1) NOT NULL,
                `status` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}newsletter_accounts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mail` VARCHAR(255) NOT NULL,
                `salutation` TINYINT(1) NOT NULL,
                `first_name` VARCHAR(255) NOT NULL,
                `last_name` VARCHAR(255) NOT NULL,
                `hash` VARCHAR(128) NOT NULL,
                `status` TINYINT(1) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX(`mail`),
                INDEX(`hash`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}newsletter_account_history` (
                `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                `date` DATETIME NOT NULL,
                `action` TINYINT(1) NOT NULL,
                INDEX (`newsletter_account_id`),
                FOREIGN KEY (`newsletter_account_id`) REFERENCES `{pre}newsletter_accounts` (`id`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}newsletter_queue` (
                `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                `newsletter_id` INT(10) UNSIGNED NOT NULL,
                UNIQUE KEY (`newsletter_account_id`, `newsletter_id`),
                INDEX (`newsletter_account_id`),
                INDEX (`newsletter_id`),
                FOREIGN KEY (`newsletter_account_id`) REFERENCES `{pre}newsletter_accounts` (`id`),
                FOREIGN KEY (`newsletter_id`) REFERENCES `{pre}newsletters` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            'DROP TABLE IF EXISTS `{pre}newsletter_account_history`;',
            'DROP TABLE IF EXISTS `{pre}newsletter_queue`;',
            'DROP TABLE IF EXISTS `{pre}newsletter_accounts`;',
            'DROP TABLE IF EXISTS `{pre}newsletters`;',
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
            'html' => 1,
        ];
    }
}
