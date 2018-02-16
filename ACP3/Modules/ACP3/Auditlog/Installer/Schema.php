<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'auditlog';

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            'CREATE TABLE `{pre}auditlog` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `entry_id` INT(10) UNSIGNED NOT NULL,
                `action` VARCHAR(255) NOT NULL,
                `data` MEDIUMBLOB NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`module_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
        ];
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
     * {@inheritdoc}
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
                    'index' => PrivilegeEnum::ADMIN_VIEW,
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
        return 1;
    }
}
