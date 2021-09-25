<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'menus';

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                ],
                'items' => [
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'order' => PrivilegeEnum::ADMIN_CREATE,
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
            'CREATE TABLE `{pre}menus` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `index_name` VARCHAR(10) NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `index_name` (`index_name`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}menu_items` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mode` TINYINT(1) UNSIGNED NOT NULL,
                `block_id` INT(10) UNSIGNED NOT NULL,
                `root_id` INT(10) UNSIGNED NOT NULL,
                `parent_id` INT(10) UNSIGNED NOT NULL,
                `left_id` INT(10) UNSIGNED NOT NULL,
                `right_id` INT(10) UNSIGNED NOT NULL,
                `display` TINYINT(1) UNSIGNED NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `uri` VARCHAR(120) NOT NULL,
                `target` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `foreign_block_id` (`block_id`),
                INDEX `left_id` (`left_id`),
                FOREIGN KEY (`block_id`) REFERENCES `{pre}menus` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}menus`;',
            'DROP TABLE IF EXISTS `{pre}menu_items`;',
        ];
    }

    public function settings(): array
    {
        return [];
    }
}
