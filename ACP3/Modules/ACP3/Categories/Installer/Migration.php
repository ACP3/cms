<?php
namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\Installer\AbstractMigration;

class Migration extends AbstractMigration
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
                "ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            33 => [
                "ALTER TABLE `{pre}categories` ENGINE = InnoDB",
            ],
            34 => [
                "DELETE FROM `{pre}categories` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);",
                "ALTER TABLE `{pre}categories` ADD INDEX (`module_id`)",
                "ALTER TABLE `{pre}categories` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE"
            ],
            35 => [
                "ALTER TABLE `{pre}categories` ADD COLUMN `root_id` INT(10) UNSIGNED NOT NULL AFTER `id`;",
                "ALTER TABLE `{pre}categories` ADD COLUMN `parent_id` INT(10) UNSIGNED NOT NULL AFTER `root_id`;",
                "ALTER TABLE `{pre}categories` ADD COLUMN `left_id` INT(10) UNSIGNED NOT NULL AFTER `parent_id`;",
                "ALTER TABLE `{pre}categories` ADD COLUMN `right_id` INT(10) UNSIGNED NOT NULL AFTER `left_id`;",
                "ALTER TABLE `{pre}categories` ADD INDEX `left_id` (`left_id`);",
                "UPDATE `{pre}categories` SET `root_id` = `id`, `parent_id` = 0;"
            ],
            36 => [
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
