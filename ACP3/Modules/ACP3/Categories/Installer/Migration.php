<?php
namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Categories\Installer
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
