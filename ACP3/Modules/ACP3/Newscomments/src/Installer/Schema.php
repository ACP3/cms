<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\Installer;

use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'newscomments';

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
        return 1;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'ALTER TABLE `{pre}news` ADD COLUMN `comments` TINYINT(1) UNSIGNED NOT NULL;',
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            'ALTER TABLE `{pre}news` DROP COLUMN `comments`;',
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
