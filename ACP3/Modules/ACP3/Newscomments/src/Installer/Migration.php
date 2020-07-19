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
        return [
            2 => [
                'ALTER TABLE `{pre}news` MODIFY `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function renameModule()
    {
        return [];
    }
}
