<?php

namespace ACP3\Modules\ACP3\Acp\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    const MODULE_NAME = 'acp';

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
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW
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
        return 30;
    }
}
