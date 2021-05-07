<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    public const MODULE_NAME = 'feeds';

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
            'feed_type' => 'RSS 2.0',
        ];
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
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
        return 32;
    }
}
