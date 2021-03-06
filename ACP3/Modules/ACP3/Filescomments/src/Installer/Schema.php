<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filescomments\Installer;

use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    public const MODULE_NAME = 'filescomments';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 2;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'ALTER TABLE `{pre}files` ADD COLUMN `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;',
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            'ALTER TABLE `{pre}files` DROP COLUMN `comments`;',
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'comments' => 1,
        ];
    }
}
