<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'articles';

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
    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'duplicate' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                ],
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'details' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'single' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}articles` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `subtitle` VARCHAR(255) NOT NULL,
                `text` TEXT NOT NULL,
                `layout` VARCHAR(120) NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                FULLTEXT KEY `index` (`title`, `text`),
                INDEX (`active`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return ['DROP TABLE IF EXISTS `{pre}articles`;'];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [];
    }
}
