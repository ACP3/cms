<?php

namespace ACP3\Modules\ACP3\Search\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Search\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
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
        return 'search';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 32;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }
}
