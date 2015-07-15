<?php

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\System\Installer
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
                'Extensions' => [
                    'index' => 7,
                    'designs' => 7,
                    'languages' => 7,
                    'modules' => 7,
                ],
                'Index' => [
                    'configuration' => 7,
                ],
                'Maintenance' => [
                    'cache' => 7,
                    'sql_export' => 7,
                    'sql_import' => 7,
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'system';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 54;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}modules` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `version` TINYINT(3) UNSIGNED NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}sessions` (
                `session_id` VARCHAR(32) NOT NULL,
                `session_starttime` INT(10) UNSIGNED NOT NULL,
                `session_data` TEXT NOT NULL,
                PRIMARY KEY (`session_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}settings` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `module_id` INT(10) NOT NULL,
                `name` VARCHAR(40) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id`), UNIQUE KEY (`module_id`,`name`)
            ) {ENGINE} {CHARSET};",
            // ACL
            "CREATE TABLE `{pre}acl_privileges` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `key` VARCHAR(100) NOT NULL,
                `description` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `key` (`key`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE`{pre}acl_resources` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `module_id` int(10) unsigned NOT NULL,
                `area` VARCHAR(255) NOT NULL,
                `controller` VARCHAR(255) NOT NULL,
                `page` varchar(255) NOT NULL,
                `params` varchar(255) NOT NULL,
                `privilege_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE`{pre}acl_roles` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `root_id` int(10) unsigned NOT NULL,
                `parent_id` int(10) unsigned NOT NULL,
                `left_id` int(10) unsigned NOT NULL,
                `right_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE`{pre}acl_rules` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `role_id` int(10) unsigned NOT NULL,
                `module_id` int(10) unsigned NOT NULL,
                `privilege_id` int(10) unsigned NOT NULL,
                `permission` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `role_id` (`role_id`,`module_id`,`privilege_id`)
            ) {engine} {charset};",
            "CREATE TABLE`{pre}acl_user_roles` (
                `user_id` int(10) unsigned NOT NULL,
                `role_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`user_id`,`role_id`)
            ) {engine} {charset};",
            // Default Privileges and user roles
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (1, 'view', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (2, 'create', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (3, 'admin_view', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (4, 'admin_create', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (5, 'admin_edit', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (6, 'admin_delete', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (7, 'admin_settings', '');",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 0, 1, 8)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (2, 'Mitglied', 1, 1, 2, 7)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (3, 'Autor', 1, 2, 3, 6)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (4, 'Administrator', 1, 3, 4, 5);",
            "INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (0, 1)",
            "INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (1, 4);"
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
            'cache_images' => true,
            'cache_minify' => 3600,
            'date_format_long' => '',
            'date_format_short' => '',
            'date_time_zone' => '',
            'design' => 'acp3',
            'entries' => '',
            'flood' => '20',
            'homepage' => 'news/index/index/',
            'lang' => '',
            'mailer_smtp_auth' => false,
            'mailer_smtp_host' => '',
            'mailer_smtp_password' => '',
            'mailer_smtp_port' => 25,
            'mailer_smtp_security' => '',
            'mailer_smtp_user' => '',
            'mailer_type' => 'mail',
            'maintenance_mode' => false,
            'maintenance_message' => '',
            'wysiwyg' => 'core.wysiwyg.ckeditor'
        ];
    }
}
