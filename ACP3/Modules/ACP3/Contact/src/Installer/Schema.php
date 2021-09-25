<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'contact';

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}contacts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `name` VARCHAR(80) NOT NULL,
                `message` TEXT NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}contacts`;',
        ];
    }

    public function settings(): array
    {
        return [
            'address' => '',
            'ceo' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'mobile_phone' => '',
            'picture_credits' => '',
            'telephone' => '',
            'vat_id' => '',
        ];
    }

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'imprint' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }
}
