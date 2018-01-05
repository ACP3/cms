<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'system';

    /**
     * @return array
     */
    public function specialResources()
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
        return 70;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'CREATE TABLE `{pre}modules` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `version` TINYINT(3) UNSIGNED NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}sessions` (
                `session_id` VARCHAR(32) NOT NULL,
                `session_starttime` INT(10) UNSIGNED NOT NULL,
                `session_data` TEXT NOT NULL,
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
            'cache_lifetime' => 3600,
            'cache_images' => true,
            'cache_vary_user' => 0,
            'cookie_consent_is_enabled' => 0,
            'cookie_consent_text' => '',
            'date_format_long' => '',
            'date_format_short' => '',
            'date_time_zone' => '',
            'design' => 'acp3',
            'entries' => '20',
            'flood' => '20',
            'homepage' => 'news/index/index/',
            'lang' => '',
            'mailer_smtp_auth' => false,
            'mailer_smtp_host' => '',
            'mailer_smtp_password' => '',
            'mailer_smtp_port' => 25,
            'mailer_smtp_security' => 'none',
            'mailer_smtp_user' => '',
            'mailer_type' => 'mail',
            'maintenance_mode' => false,
            'maintenance_message' => '',
            'mod_rewrite' => false,
            'page_cache_is_enabled' => false,
            'page_cache_is_valid' => true,
            'page_cache_purge_mode' => 1,
            'security_secret' => \uniqid(\mt_rand(), true),
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
