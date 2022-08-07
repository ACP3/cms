<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filessearch\Installer;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'filessearch';

    public function createTables(): array
    {
        return [
            'ALTER TABLE `{pre}files` ADD FULLTEXT `fulltext_index_title` (`title`, `file`);',
            'ALTER TABLE `{pre}files` ADD FULLTEXT `fulltext_index_content` (`text`);',
            'ALTER TABLE `{pre}files` ADD FULLTEXT `fulltext_index_title_content` (`title`, `file`, `text`);',
        ];
    }

    public function removeTables(): array
    {
        return [
            'ALTER TABLE `{pre}files` DROP INDEX `fulltext_index_title`;',
            'ALTER TABLE `{pre}files` DROP INDEX `fulltext_index_content`;',
            'ALTER TABLE `{pre}files` DROP INDEX `fulltext_index_title_content`;',
        ];
    }

    public function settings(): array
    {
        return [];
    }

    public function specialResources(): array
    {
        return [];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }
}
