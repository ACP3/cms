<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Installer;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'gallerycomments';

    public function specialResources(): array
    {
        return [];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    public function createTables(): array
    {
        return [
            'ALTER TABLE `{pre}gallery` ADD COLUMN `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;',
        ];
    }

    public function removeTables(): array
    {
        return [
            'ALTER TABLE `{pre}gallery` DROP COLUMN `comments`;',
        ];
    }

    public function settings(): array
    {
        return [
            'comments' => 1,
        ];
    }
}
