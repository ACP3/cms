<?php

namespace ACP3\Modules\Minify;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Minify
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'minify';
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