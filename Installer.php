<?php

namespace ACP3\Modules\ACP3\WYSIWYGCKEditor;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\WYSIWYGCKEditor
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'wysiwygckeditor';
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
