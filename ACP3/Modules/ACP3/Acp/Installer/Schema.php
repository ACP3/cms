<?php

namespace ACP3\Modules\ACP3\Acp\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Acp\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
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

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [];
    }

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
        return 'acp';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 30;
    }
}
