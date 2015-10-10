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
            ]
        ];
    }
}
