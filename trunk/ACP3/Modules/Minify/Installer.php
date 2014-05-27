<?php

namespace ACP3\Modules\Minify;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'minify';
    const SCHEMA_VERSION = 1;

    public function createTables()
    {
        return array();
    }

    public function removeTables()
    {
        return array();
    }

    public function settings()
    {
        return array();
    }

    public function schemaUpdates()
    {
        return array();
    }

}