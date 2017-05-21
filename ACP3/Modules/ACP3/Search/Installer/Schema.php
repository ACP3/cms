<?php

namespace ACP3\Modules\ACP3\Search\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Search\Installer
 */
class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    const MODULE_NAME = 'search';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ]
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
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
        return 34;
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
