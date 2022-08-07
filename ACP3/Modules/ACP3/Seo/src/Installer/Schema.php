<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'seo';

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
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                    'sitemap' => PrivilegeEnum::ADMIN_SETTINGS,
                    'suggest' => PrivilegeEnum::ADMIN_VIEW,
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
            'CREATE TABLE IF NOT EXISTS `{pre}seo` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uri` VARCHAR(191) NOT NULL,
                `alias` VARCHAR(100) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `keywords` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `canonical` VARCHAR(255) NOT NULL,
                `robots` TINYINT(1) UNSIGNED NOT NULL,
                `structured_data` TEXT,
                PRIMARY KEY (`id`), UNIQUE(`uri`), INDEX (`alias`)
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}seo`;',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [
            'meta_description' => '',
            'meta_keywords' => '',
            'robots' => 1,
            'index_paginated_content' => 'first',
            'sitemap_is_enabled' => 0,
            'sitemap_save_mode' => 2,
            'sitemap_separate' => 0,
        ];
    }
}
