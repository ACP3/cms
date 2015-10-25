<?php

namespace ACP3\Modules\ACP3\Permissions\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Permissions\Installer
 */
class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * @inheritdoc
     * 
     * @return array
     */
    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET `name` = 'permissions' WHERE `name` = 'access';"
        ];
    }

    /**
     * @inheritdoc
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
                "ALTER TABLE `{pre}acl_privileges` ENGINE = InnoDB",
                "ALTER TABLE `{pre}acl_resources` ENGINE = InnoDB",
                "ALTER TABLE `{pre}acl_roles` ENGINE = InnoDB",
                "ALTER TABLE `{pre}acl_rules` ENGINE = InnoDB",
                "ALTER TABLE `{pre}acl_user_roles` ENGINE = InnoDB",
            ],
            34 => [
                "ALTER TABLE `{pre}acl_resources` ADD INDEX (`privilege_id`)",
                "ALTER TABLE `{pre}acl_resources` ADD FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE",
                "DELETE FROM `{pre}acl_rules` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);",
                "ALTER TABLE `{pre}acl_rules` ADD INDEX (`role_id`)",
                "ALTER TABLE `{pre}acl_rules` ADD INDEX (`privilege_id`)",
                "ALTER TABLE `{pre}acl_rules` ADD INDEX (`module_id`)",
                "ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE",
                "ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`privilege_id`) REFERENCES `{pre}acl_privileges` (`id`) ON DELETE CASCADE",
                "ALTER TABLE `{pre}acl_rules` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE",
                "ALTER TABLE `{pre}acl_user_roles` ADD INDEX (`role_id`)",
                "ALTER TABLE `{pre}acl_user_roles` ADD FOREIGN KEY (`role_id`) REFERENCES `{pre}acl_roles` (`id`) ON DELETE CASCADE",
            ],
            35 => [
                "DELETE FROM `{pre}acl_user_roles` WHERE `user_id` = 0",
                "ALTER TABLE `{pre}acl_user_roles` CHANGE `user_id` `user_id` INT(10) UNSIGNED",
                "ALTER TABLE `{pre}acl_user_roles` ADD INDEX (`user_id`)",
                "ALTER TABLE `{pre}acl_user_roles` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE CASCADE"
            ]
        ];
    }
}
