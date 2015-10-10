<?php

namespace ACP3\Modules\ACP3\Comments\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Comments\Installer
 */
class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * @inheritdoc
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', '{moduleId}', 'acp_list_comments', '', 3);",
                "UPDATE `{pre}acl_resources` SET `page` = 'acp_delete' WHERE `module_id` = '{moduleId}' AND `page` = 'acp_delete_comments_per_module';",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'list', '', 1);",
            ],
            33 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'details' WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_comments';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_comments', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_comments';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'details' WHERE `module_id` = '{moduleId}' AND `page` = 'edit';",
            ],
            34 => [
                "ALTER TABLE `{pre}comments` ENGINE = InnoDB",
            ],
            35 => [
                "ALTER TABLE `{pre}comments` ADD INDEX (`module_id`)",
                "ALTER TABLE `{pre}comments` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE"
            ]
        ];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
