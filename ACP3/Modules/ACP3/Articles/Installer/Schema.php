<?php

namespace ACP3\Modules\ACP3\Articles\Installer;

use ACP3\Core\ACL\PrivilegeEnum;
use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Articles
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'articles';

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
        return 40;
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
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE
                ]
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'details' => PrivilegeEnum::FRONTEND_VIEW
                ]
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'single' => PrivilegeEnum::FRONTEND_VIEW
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}articles` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                FULLTEXT KEY `index` (`title`, `text`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE IF EXISTS `{pre}articles`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }
}
