<?php

namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    const MODULE_NAME = 'news';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'admin' => [
                'index' => [
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'duplicate' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS
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
                    'latest' => PrivilegeEnum::FRONTEND_VIEW
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
        return 43;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}news` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `readmore` TINYINT(1) UNSIGNED NOT NULL,
                `comments` TINYINT(1) UNSIGNED NOT NULL,
                `category_id` INT(10) UNSIGNED,
                `uri` VARCHAR(120) NOT NULL,
                `target` TINYINT(1) UNSIGNED NOT NULL,
                `link_title` VARCHAR(120) NOT NULL,
                `user_id` INT UNSIGNED,
                PRIMARY KEY (`id`),
                FULLTEXT KEY `index` (`title`,`text`),
                INDEX (`active`),
                INDEX `foreign_category_id` (`category_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE SET NULL,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE IF EXISTS `{pre}news`;"
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'comments' => 1,
            'dateformat' => 'long',
            'readmore' => 1,
            'readmore_chars' => 350,
            'sidebar' => 5,
            'category_in_breadcrumb' => 1
        ];
    }
}
