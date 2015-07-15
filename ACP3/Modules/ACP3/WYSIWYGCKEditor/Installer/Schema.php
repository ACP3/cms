<?php

namespace ACP3\Modules\ACP3\WYSIWYGCKEditor\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\WYSIWYGCKEditor\Installer
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
        return 'wysiwygckeditor';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 1;
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
