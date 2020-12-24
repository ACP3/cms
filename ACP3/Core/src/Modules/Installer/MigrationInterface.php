<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules\Installer;

interface MigrationInterface
{
    /**
     * Returns an array with changes to the table structure and data of a module.
     *
     * @return Array<int, string|callable|string[]|callable[]>
     */
    public function schemaUpdates();

    /**
     * Returns an array with the SQL changes needed to convert a module, so that a functions with its new name.
     *
     * @return Array<int, string|callable|string[]|callable[]>
     */
    public function renameModule();
}
