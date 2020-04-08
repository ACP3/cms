<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\Installer;

use ACP3\Core\Modules;

class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function schemaUpdates()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function renameModule()
    {
        return [];
    }
}
