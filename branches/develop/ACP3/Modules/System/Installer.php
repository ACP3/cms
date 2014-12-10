<?php

namespace ACP3\Modules\System;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\System
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'system';
    const SCHEMA_VERSION = 49;

    /**
     * @var array
     */
    protected $specialResources = [
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
                'sql_export' => 7,
                'sql_import' => 7,
            ]
        ]
    ];

    /**
     * @inheritdoc
     */
    public function removeResources()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}modules` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `version` tinyint(3) UNSIGNED NOT NULL,
                `active` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}sessions` (
                `session_id` varchar(32) NOT NULL,
                `session_starttime` int(10) unsigned NOT NULL,
                `session_data` text NOT NULL,
                PRIMARY KEY (`session_id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}settings` (
                `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `module_id` INT(10) NOT NULL,
                `name` VARCHAR(40) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id`), UNIQUE KEY (`module_id`,`name`)
            ) {engine} {charset};",
            // ACL
            "CREATE TABLE `{pre}acl_privileges` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `key` varchar(100) NOT NULL,
                `description` varchar(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `key` (`key`)
            ) {engine} {charset};",
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
            // Default Privilegien & Benutzer-Rollen
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
     * @inheritdoc
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @inheritdoc
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
            'seo_meta_description' => '',
            'seo_meta_keywords' => '',
            'seo_mod_rewrite' => false,
            'seo_robots' => 1,
            'seo_title' => '',
            'wysiwyg' => 'CKEditor'
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeFromModulesTable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'extra_css', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'extra_js', '');",
            ],
            32 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'icons_path', 'libraries/crystal_project/');",
            ],
            33 => [
                "UPDATE `{pre}acl_resources` SET privilege_id = 3 WHERE module_id = " . $this->getModuleId() . " AND page = 'acp_maintenance';",
            ],
            34 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'functions', '', 1);",
            ],
            35 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
                "UPDATE `{pre}settings` SET value = \"4.0-dev\" WHERE module_id = " . $this->getModuleId() . " AND name = \"version\";",
            ],
            36 => [
                'ALTER TABLE `{pre}acl_resources` ADD COLUMN `area` VARCHAR(255) NOT NULL AFTER `module_id`;',
                'ALTER TABLE `{pre}acl_resources` ADD COLUMN `controller` VARCHAR(255) NOT NULL AFTER `area`;',
                'UPDATE `{pre}acl_resources` SET area="frontend";',
                'UPDATE `{pre}acl_resources` SET area="admin" WHERE page LIKE "acp_%";',
                'UPDATE `{pre}acl_resources` SET area="sidebar" WHERE page LIKE "sidebar%";',
            ],
            37 => [
                'UPDATE `{pre}acl_resources` SET controller="index";',
                'UPDATE `{pre}acl_resources` SET page=REPLACE(page, "acp_", "") WHERE page LIKE "acp_%";',
            ],
            38 => [
                'UPDATE `{pre}acl_resources` SET page="index" WHERE page="sidebar";',
            ],
            39 => [
                'UPDATE `{pre}acl_resources` SET page=REPLACE(page, "list", "index") WHERE page LIKE "list%";',
            ],
            40 => [
                'UPDATE `{pre}acl_resources` SET controller = "maintenance" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "sql_export";',
                'UPDATE `{pre}acl_resources` SET controller = "maintenance" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "sql_import";',
                'UPDATE `{pre}acl_resources` SET controller = "maintenance" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "update_check";',
                'UPDATE `{pre}acl_resources` SET controller = "maintenance", page = "index" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "maintenance";',
            ],
            41 => [
                'UPDATE `{pre}acl_resources` SET controller = "extensions" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "designs";',
                'UPDATE `{pre}acl_resources` SET controller = "extensions" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "languages";',
                'UPDATE `{pre}acl_resources` SET controller = "extensions" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "modules";',
                'UPDATE `{pre}acl_resources` SET controller = "extensions", page = "index" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "extensions";',
            ],
            42 => [
                'UPDATE `{pre}settings` SET value = "de_DE" WHERE module_id = ' . $this->getModuleId() . ' AND name = "lang" AND value = "de";',
                'UPDATE `{pre}settings` SET value = "en_US" WHERE module_id = ' . $this->getModuleId() . ' AND name = "lang" AND value = "en";',
            ],
            43 => [
                'DELETE FROM `{pre}acl_resources` WHERE `module_id` = ' . $this->getModuleId() . ' AND area = "admin" AND controller = "extensions" AND page = "languages";',
            ],
            44 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'cache_driver', 'PhpFile');",
            ],
            45 => [
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"seo_aliases\";",
            ],
            46 => [
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"icons_path\";",
            ],
            47 => [
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"extra_css\";",
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"extra_js\";",
            ],
            48 => [
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"cache_driver\";",
                "DELETE FROM `{pre}settings` WHERE module_id = " . $this->getModuleId() . " AND name = \"version\";",
            ],
            49 => [
                'ALTER TABLE `{pre}seo` DROP INDEX `PRIMARY`;',
                'ALTER TABLE `{pre}seo` ADD UNIQUE (uri);',
                'ALTER TABLE `{pre}seo` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;',
            ],
        ];
    }
}
