<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Installer;

use ACP3\Core\Modules\Installer\MigrationInterface;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Acp\Installer
 */
class Migration implements MigrationInterface
{

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
