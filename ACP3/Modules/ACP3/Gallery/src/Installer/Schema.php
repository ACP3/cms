<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'gallery';

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
                'pictures' => [
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'order' => PrivilegeEnum::ADMIN_CREATE,
                ],
            ],
            'frontend' => [
                'index' => [
                    'image' => PrivilegeEnum::FRONTEND_VIEW,
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'details' => PrivilegeEnum::FRONTEND_VIEW,
                    'pics' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'pictures' => PrivilegeEnum::FRONTEND_VIEW,
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
            'CREATE TABLE `{pre}gallery` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `active` TINYINT(1) NOT NULL,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `subtitle` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `user_id` INT UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}gallery_pictures` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `pic` INT(10) UNSIGNED NOT NULL,
                `gallery_id` INT(10) UNSIGNED NOT NULL,
                `file` VARCHAR(120) NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `description` TEXT NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `foreign_gallery_id` (`gallery_id`),
                FOREIGN KEY (`gallery_id`) REFERENCES `{pre}gallery` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}gallery_pictures`;',
            'DROP TABLE IF EXISTS `{pre}gallery`;',
        ];
    }

    public function settings(): array
    {
        return [
            'width' => 640,
            'height' => 480,
            'thumbwidth' => 160,
            'thumbheight' => 120,
            'dateformat' => 'long',
            'sidebar' => 5,
        ];
    }
}
