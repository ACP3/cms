<?php

namespace ACP3\Modules\ACP3\WYSIWYGTinyMCE\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\WYSIWYGTinyMCE\Installer
 */
class Migration implements Modules\Installer\MigrationInterface
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
