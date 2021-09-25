<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'polls';

    public function specialResources(): array
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                ],
            ],
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                    'result' => PrivilegeEnum::FRONTEND_VIEW,
                    'vote' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
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
            'CREATE TABLE `{pre}polls` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `multiple` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED,
                PRIMARY KEY (`id`),
                INDEX (`user_id`),
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}poll_answers` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `text` VARCHAR(120) NOT NULL,
                `poll_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                INDEX `foreign_poll_id` (`poll_id`),
                FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE
            ) {ENGINE} {CHARSET};',
            'CREATE TABLE `{pre}poll_votes` (
                `poll_id` INT(10) UNSIGNED NOT NULL,
                `answer_id` INT(10) UNSIGNED NOT NULL,
                `user_id` INT(10) UNSIGNED,
                `ip` VARCHAR(40) NOT NULL,
                `time` DATETIME NOT NULL,
                INDEX (`poll_id`),
                INDEX (`answer_id`),
                INDEX (`user_id`),
                FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`answer_id`) REFERENCES `{pre}poll_answers` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL
            ) {ENGINE} {CHARSET};',
        ];
    }

    public function removeTables(): array
    {
        return [
            'DROP TABLE IF EXISTS `{pre}poll_votes`;',
            'DROP TABLE IF EXISTS `{pre}poll_answers`;',
            'DROP TABLE IF EXISTS `{pre}polls`;',
        ];
    }

    public function settings(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function removeSettings()
    {
        return true;
    }
}
