<?php

namespace ACP3\Modules\ACP3\Permissions\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Permissions\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'permissions';

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
        return 35;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}acl_privileges` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `key` VARCHAR(100) NOT NULL,
                `description` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `key` (`key`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}acl_roles` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `root_id` int(10) unsigned NOT NULL,
                `parent_id` int(10) unsigned NOT NULL,
                `left_id` int(10) unsigned NOT NULL,
                `right_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}acl_resources` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `module_id` int(10) unsigned NOT NULL,
                `area` VARCHAR(255) NOT NULL,
                `controller` VARCHAR(255) NOT NULL,
                `page` varchar(255) NOT NULL,
                `params` varchar(255) NOT NULL,
                `privilege_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`privilege_id`),
                FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE
            ) {engine} {charset};",
            "CREATE TABLE `{pre}acl_rules` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `role_id` int(10) unsigned NOT NULL,
                `module_id` int(10) unsigned NOT NULL,
                `privilege_id` int(10) unsigned NOT NULL,
                `permission` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `role_id` (`role_id`,`module_id`,`privilege_id`),
                INDEX (`role_id`),
                INDEX (`privilege_id`),
                INDEX (`module_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE
            ) {engine} {charset};",
            "CREATE TABLE `{pre}acl_user_roles` (
                `user_id` int(10) unsigned,
                `role_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`user_id`,`role_id`),
                INDEX (`user_id`),
                INDEX (`role_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE CASCADE
            ) {engine} {charset};",
            // Default Privileges and user roles
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::FRONTEND_VIEW . ", 'view', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::FRONTEND_CREATE . ", 'create', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::ADMIN_VIEW . ", 'admin_view', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::ADMIN_CREATE . ", 'admin_create', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::ADMIN_EDIT . ", 'admin_edit', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::ADMIN_DELETE . ", 'admin_delete', '')",
            "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (" . PrivilegeEnum::ADMIN_SETTINGS . ", 'admin_settings', '');",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 0, 1, 8)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (2, 'Mitglied', 1, 1, 2, 7)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (3, 'Autor', 1, 2, 3, 6)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (4, 'Administrator', 1, 3, 4, 5);",
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
        return [];
    }
}
