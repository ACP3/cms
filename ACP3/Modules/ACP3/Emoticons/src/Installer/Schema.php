<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'emoticons';

    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}emoticons` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(10) NOT NULL,
                `description` VARCHAR(15) NOT NULL,
                `img` VARCHAR(40) NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return ['DROP TABLE IF EXISTS `{pre}emoticons`;'];
    }

    public function settings(): array
    {
        return [
            'width' => 32,
            'height' => 32,
            'filesize' => 10240,
        ];
    }

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
        ];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }
}
