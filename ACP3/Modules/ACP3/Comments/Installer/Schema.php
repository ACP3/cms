<?php

namespace ACP3\Modules\ACP3\Comments\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    const MODULE_NAME = 'comments';

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}comments` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` VARCHAR(40) NOT NULL,
                `date` DATETIME NOT NULL,
                `name` VARCHAR(20) NOT NULL,
                `user_id` INT(10) UNSIGNED,
                `message` TEXT NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `entry_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX (`module_id`, `entry_id`),
                INDEX (`module_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE IF EXISTS `{pre}comments`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'dateformat' => 'long',
            'emoticons' => 1,
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
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS
                ],
                'details' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE
                ]
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'create' => PrivilegeEnum::FRONTEND_CREATE
                ]
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
        return 36;
    }
}
