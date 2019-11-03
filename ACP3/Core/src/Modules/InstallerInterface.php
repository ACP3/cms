<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules;

use ACP3\Core\Modules\Installer\SchemaInterface;

interface InstallerInterface
{
    /**
     * @return bool
     */
    public function install(SchemaInterface $schema);

    /**
     * @return bool
     */
    public function uninstall(SchemaInterface $schema);
}
