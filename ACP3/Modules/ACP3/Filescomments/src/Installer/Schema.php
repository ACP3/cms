<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Installer;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'filescomments';

    /**
     * {@inheritDoc}
     */
    public function specialResources(): array
    {
        return [];
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
            'ALTER TABLE `{pre}files` ADD COLUMN `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return [
            'ALTER TABLE `{pre}files` DROP COLUMN `comments`;',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [
            'comments' => 1,
        ];
    }
}
