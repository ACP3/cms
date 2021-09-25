<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'permissions';

    /**
     * {@inheritDoc}
     */
    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'order' => PrivilegeEnum::ADMIN_CREATE,
                ],
                'resources' => [
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}acl_roles` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `root_id` int(10) unsigned NOT NULL,
                `parent_id` int(10) unsigned NOT NULL,
                `left_id` int(10) unsigned NOT NULL,
                `right_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};',
            'CREATE TABLE `{pre}acl_resources` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `module_id` int(10) unsigned NOT NULL,
                `area` VARCHAR(255) NOT NULL,
                `controller` VARCHAR(255) NOT NULL,
                `page` varchar(255) NOT NULL,
                `params` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`module_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE
            ) {engine} {charset};',
            'CREATE TABLE `{pre}acl_permission` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `role_id` int(10) unsigned NOT NULL,
                `resource_id` int(10) unsigned NOT NULL,
                `permission` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `role_resource` (`role_id`, `resource_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`resource_id`) REFERENCES `{pre}acl_resources` (`id`) ON DELETE CASCADE
            ) {engine} {charset};',
            'CREATE TABLE `{pre}acl_user_roles` (
                `user_id` int(10) unsigned,
                `role_id` int(10) unsigned NOT NULL,
                PRIMARY KEY (`user_id`,`role_id`),
                INDEX (`user_id`),
                INDEX (`role_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE CASCADE
            ) {engine} {charset};',
            // Default user roles
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 0, 1, 8)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (2, 'Mitglied', 1, 1, 2, 7)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (3, 'Autor', 1, 2, 3, 6)",
            "INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (4, 'Administrator', 1, 3, 4, 5);",
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [];
    }
}
