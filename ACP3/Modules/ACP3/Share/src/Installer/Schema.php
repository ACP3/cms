<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'share';

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
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'rate' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
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
            'CREATE TABLE IF NOT EXISTS `{pre}share` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uri` VARCHAR(191) NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                `services` TEXT NOT NULL,
                `ratings_active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE(`uri`)
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE IF NOT EXISTS `{pre}share_ratings` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `stars` TINYINT(1) UNSIGNED NOT NULL,
                `ip` VARCHAR(40) NOT NULL,
                `share_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX(`share_id`),
                FOREIGN KEY (`share_id`) REFERENCES `{pre}share` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}share_ratings`;',
            'DROP TABLE IF EXISTS `{pre}share`;',
        ];
    }

    public function settings(): array
    {
        return [
            'services' => '',
            'fb_app_id' => '',
            'fb_secret' => '',
        ];
    }
}
