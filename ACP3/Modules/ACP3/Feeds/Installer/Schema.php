<?php

namespace ACP3\Modules\ACP3\Feeds\Installer;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Feeds
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'feeds';

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
        return [
            'feed_image' => '',
            'feed_type' => 'RSS 2.0'
        ];
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'Admin' => [
                'Index' => [
                    'index' => 7
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 31;
    }
}
