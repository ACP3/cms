<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Application\BootstrapInterface;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'system';

    public function specialResources(): array
    {
        return [
            'admin' => [
                'extensions' => [
                    'designs' => PrivilegeEnum::ADMIN_SETTINGS,
                    'index' => PrivilegeEnum::ADMIN_SETTINGS,
                    'modules' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
                'maintenance' => [
                    'cache' => PrivilegeEnum::ADMIN_SETTINGS,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'update_check' => PrivilegeEnum::ADMIN_VIEW,
                ],
            ],
        ];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}migration` (
                `name` VARCHAR(255) NOT NULL,
                UNIQUE KEY `migrationName` (`name`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}modules` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}sessions` (
                `session_id` VARCHAR(128) NOT NULL,
                `session_starttime` INT(10) UNSIGNED NOT NULL,
                `session_lifetime` INT(10) UNSIGNED NOT NULL,
                `session_data` MEDIUMBLOB NOT NULL,
                PRIMARY KEY (`session_id`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}settings` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `name` VARCHAR(40) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY (`module_id`,`name`),
                INDEX (`module_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [];
    }

    public function settings(): array
    {
        return [
            'cache_lifetime' => 3600,
            'cache_images' => 1,
            'cache_vary_user' => 0,
            'date_format_long' => '',
            'date_format_short' => '',
            'date_time_zone' => '',
            'design' => 'acp3',
            'entries' => '20',
            'flood' => '20',
            'homepage' => 'news/index/index/',
            'lang' => 'en_US',
            'mailer_smtp_auth' => 0,
            'mailer_smtp_host' => '',
            'mailer_smtp_password' => '',
            'mailer_smtp_port' => 25,
            'mailer_smtp_security' => 'none',
            'mailer_smtp_user' => '',
            'mailer_type' => 'mail',
            'maintenance_mode' => 0,
            'maintenance_message' => '',
            'mod_rewrite' => 0,
            'page_cache_is_enabled' => 0,
            'page_cache_is_valid' => 1,
            'page_cache_purge_mode' => 1,
            'security_secret' => uniqid((string) mt_rand(), true),
            'site_title' => '',
            'site_subtitle' => '',
            'site_subtitle_homepage_mode' => 0,
            'site_subtitle_mode' => 1,
            'update_last_check' => 0,
            'update_new_version' => BootstrapInterface::VERSION,
            'update_new_version_url' => '',
            'wysiwyg' => 'core.wysiwyg.textarea',
        ];
    }
}
