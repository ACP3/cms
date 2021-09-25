<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'categories';

    /**
     * {@inheritDoc}
     */
    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}categories` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `root_id` INT(10) UNSIGNED NOT NULL,
                `parent_id` INT(10) UNSIGNED NOT NULL,
                `left_id` INT(10) UNSIGNED NOT NULL,
                `right_id` INT(10) UNSIGNED NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `picture` VARCHAR(120) NOT NULL,
                `description` VARCHAR(120) NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `left_id` (`left_id`),
                INDEX (`module_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return ['DROP TABLE IF EXISTS `{pre}categories`;'];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [
            'width' => 100,
            'height' => 50,
            'filesize' => 40960,
        ];
    }

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
                    'order' => PrivilegeEnum::ADMIN_EDIT,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
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
}
