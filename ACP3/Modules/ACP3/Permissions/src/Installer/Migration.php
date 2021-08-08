<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Installer;

use ACP3\Core\Modules;

class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET `name` = 'permissions' WHERE `name` = 'access';",
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            32 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'resources' WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_resource%';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_resources', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_resources';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_resource', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_resource';",
            ],
            33 => [
                'ALTER TABLE `{pre}acl_privileges` ENGINE = InnoDB',
                'ALTER TABLE `{pre}acl_resources` ENGINE = InnoDB',
                'ALTER TABLE `{pre}acl_roles` ENGINE = InnoDB',
                'ALTER TABLE `{pre}acl_rules` ENGINE = InnoDB',
                'ALTER TABLE `{pre}acl_user_roles` ENGINE = InnoDB',
            ],
            34 => [
                'ALTER TABLE `{pre}acl_resources` ADD INDEX (`privilege_id`)',
                'ALTER TABLE `{pre}acl_resources` ADD FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE',
                'DELETE FROM `{pre}acl_rules` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);',
                'ALTER TABLE `{pre}acl_rules` ADD INDEX (`role_id`)',
                'ALTER TABLE `{pre}acl_rules` ADD INDEX (`privilege_id`)',
                'ALTER TABLE `{pre}acl_rules` ADD INDEX (`module_id`)',
                'ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE',
                'ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE',
                'ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE',
                'ALTER TABLE `{pre}acl_user_roles` ADD INDEX (`role_id`)',
                'ALTER TABLE `{pre}acl_user_roles` ADD FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE',
            ],
            35 => [
                'DELETE FROM `{pre}acl_user_roles` WHERE `user_id` = 0',
                'ALTER TABLE `{pre}acl_user_roles` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}acl_user_roles` ADD INDEX (`user_id`)',
                'ALTER TABLE `{pre}acl_user_roles` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE CASCADE',
            ],
            36 => [
                'DELETE FROM `{pre}acl_resources` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);',
                'ALTER TABLE `{pre}acl_resources` ADD INDEX (`module_id`)',
                'ALTER TABLE `{pre}acl_resources` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE',
            ],
            37 => [
                'ALTER TABLE `{pre}acl_privileges` MODIFY COLUMN `key` VARCHAR(100) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_privileges` MODIFY COLUMN `description` VARCHAR(100) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_privileges` CONVERT TO {charset};',
                'ALTER TABLE `{pre}acl_roles` MODIFY COLUMN `name` VARCHAR(100) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_roles` CONVERT TO {charset};',
                'ALTER TABLE `{pre}acl_resources` MODIFY COLUMN `area` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_resources` MODIFY COLUMN `controller` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_resources` MODIFY COLUMN `page` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_resources` MODIFY COLUMN `params` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}acl_resources` CONVERT TO {charset};',
                'ALTER TABLE `{pre}acl_rules` CONVERT TO {charset};',
                'ALTER TABLE `{pre}acl_user_roles` CONVERT TO {charset};',
            ],
            38 => [
                'CREATE TABLE `{pre}acl_permission` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `role_id` int(10) unsigned NOT NULL,
                `resource_id` int(10) unsigned NOT NULL,
                `permission` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `role_resource` (`role_id`, `resource_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`resource_id`) REFERENCES `{pre}acl_resources` (`id`) ON DELETE CASCADE
            ) {engine} {charset};',
            ],
            39 => [
                "insert into `{pre}acl_permission` (`role_id`, `resource_id`, `permission`)
select aru.role_id, are.id, IF(aru.permission = 0, 2, aru.permission)
from `{pre}acl_resources` are
left join `{pre}modules` m on (m.id = are.module_id)
left join `{pre}acl_rules` aru on (aru.module_id = are.module_id and aru.privilege_id = are.privilege_id)
group by are.id, aru.role_id, aru.permission;
",
            ],
        ];
    }
}
