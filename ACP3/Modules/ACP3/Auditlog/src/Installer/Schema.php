<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'auditlog';

    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}auditlog` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `table_name` varchar(255) NOT NULL,
                `entry_id` INT(10) UNSIGNED NOT NULL,
                `action` VARCHAR(255) NOT NULL,
                `data` MEDIUMBLOB NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`module_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [];
    }

    public function settings(): array
    {
        return [];
    }

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'table' => PrivilegeEnum::ADMIN_VIEW,
                ],
            ],
        ];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }
}
