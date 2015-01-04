<?php

namespace ACP3\Modules\Comments;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Comments
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'comments';
    const SCHEMA_VERSION = 33;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}comments` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` VARCHAR(40) NOT NULL,
                `date` DATETIME NOT NULL,
                `name` VARCHAR(20) NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                `message` TEXT NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                `entry_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX (`module_id`, `entry_id`)
            ) {engine} {charset};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}comments`;"];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'dateformat' => 'long',
            'emoticons' => 1,
        ];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'acp_list_comments', '', 3);",
                "UPDATE `{pre}acl_resources` SET `page` = 'acp_delete' WHERE `module_id` = " . $this->getModuleId() . " AND `page` = 'acp_delete_comments_per_module';",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"functions\";",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'list', '', 1);",
            ],
            33 => [
                'UPDATE `{pre}acl_resources` SET controller = "details" WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_comments";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_comments", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` LIKE "%_comments";',
                'UPDATE `{pre}acl_resources` SET controller = "details" WHERE `module_id` = ' . $this->getModuleId() . ' AND `page` = "edit";',
            ]
        ];
    }
}
