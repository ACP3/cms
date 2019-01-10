<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'contact';

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
        return 43;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'CREATE TABLE `{pre}contacts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `name` VARCHAR(80) NOT NULL,
                `message` TEXT NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            'DROP TABLE IF EXISTS `{pre}contacts`;',
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'address' => '',
            'ceo' => '',
            'disclaimer' => '',
            'fax' => '',
            'mail' => '',
            'mobile_phone' => '',
            'picture_credits' => '',
            'telephone' => '',
            'vat_id' => '',
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
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'imprint' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }
}
