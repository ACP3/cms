<?php

namespace ACP3\Modules\ACP3\Filemanager;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Filemanager
 */
class Installer extends Modules\SchemaInstaller
{
    const MODULE_NAME = 'filemanager';
    const SCHEMA_VERSION = 1;


    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [];
    }
}
