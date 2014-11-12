<?php

namespace ACP3\Modules\Acp;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Acp
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'acp';
    const SCHEMA_VERSION = 30;

    /**
     * @inheritdoc
     */
    public function removeResources()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeFromModulesTable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return array();
    }

}