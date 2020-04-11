<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'guestbook';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'index' => [
                    'create' => PrivilegeEnum::FRONTEND_CREATE,
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
        return 37;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'CREATE TABLE `{pre}guestbook` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `ip` VARCHAR(40) NOT NULL,
                `name` VARCHAR(20) NOT NULL,
                `user_id` INT(10) UNSIGNED,
                `message` TEXT NOT NULL,
                `website` VARCHAR(120) NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `foreign_user_id` (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ['DROP TABLE IF EXISTS `{pre}guestbook`;'];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'dateformat' => 'long',
            'notify' => 0,
            'notify_email' => '',
            'overlay' => 1,
        ];
    }
}
