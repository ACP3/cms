<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'users';

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS,
                ],
            ],
            'frontend' => [
                'account' => [
                    'edit' => PrivilegeEnum::FRONTEND_VIEW,
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'settings' => PrivilegeEnum::FRONTEND_VIEW,
                ],
                'index' => [
                    'forgot_pwd' => PrivilegeEnum::FRONTEND_VIEW,
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'login' => PrivilegeEnum::FRONTEND_VIEW,
                    'logout' => PrivilegeEnum::FRONTEND_VIEW,
                    'register' => PrivilegeEnum::FRONTEND_VIEW,
                    'view_profile' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'hash' => PrivilegeEnum::FRONTEND_VIEW,
                    'login' => PrivilegeEnum::FRONTEND_VIEW,
                    'user_menu' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    public function createTables(): array
    {
        return [
            'CREATE TABLE `{pre}users` (
                `id` INT(10) UNSIGNED AUTO_INCREMENT,
                `super_user` TINYINT(1) UNSIGNED NOT NULL,
                `nickname` VARCHAR(30) NOT NULL,
                `pwd` VARCHAR(128) NOT NULL,
                `pwd_salt` VARCHAR(16) NOT NULL,
                `remember_me_token` VARCHAR(128) NOT NULL,
                `login_errors` TINYINT(1) UNSIGNED NOT NULL,
                `realname` VARCHAR(80) NOT NULL,
                `gender` TINYINT(1) NOT NULL,
                `birthday` VARCHAR(10) NOT NULL,
                `birthday_display` TINYINT(1) UNSIGNED NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `mail_display` TINYINT(1) UNSIGNED NOT NULL,
                `website` VARCHAR(120) NOT NULL,
                `icq` VARCHAR(11) NOT NULL,
                `skype` VARCHAR(30) NOT NULL,
                `street` VARCHAR(120) NOT NULL,
                `house_number` VARCHAR(5) NOT NULL,
                `zip` VARCHAR(6) NOT NULL,
                `city` VARCHAR(120) NOT NULL,
                `address_display` TINYINT(1) UNSIGNED NOT NULL,
                `country` CHAR(2) NOT NULL,
                `country_display` TINYINT(1) UNSIGNED NOT NULL,
                `registration_date` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `nickname` (`nickname`),
                UNIQUE KEY `mail` (`mail`)
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [];
    }

    public function settings(): array
    {
        return [
            'enable_registration' => 1,
            'mail' => '',
        ];
    }
}
