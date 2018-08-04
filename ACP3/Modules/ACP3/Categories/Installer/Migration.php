<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\Modules;

class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                'ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;',
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            33 => [
                'ALTER TABLE `{pre}categories` ENGINE = InnoDB',
            ],
            34 => [
                'DELETE FROM `{pre}categories` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);',
                'ALTER TABLE `{pre}categories` ADD INDEX (`module_id`)',
                'ALTER TABLE `{pre}categories` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE',
            ],
            35 => [
                "ALTER TABLE `{pre}categories` CONVERT TO {charset};",
                "ALTER TABLE `{pre}categories` MODIFY COLUMN `title` VARCHAR(120) {charset} NOT NULL;",
                "ALTER TABLE `{pre}categories` MODIFY COLUMN `picture` VARCHAR(120) {charset} NOT NULL;",
                "ALTER TABLE `{pre}categories` MODIFY COLUMN `description` VARCHAR(120) {charset} NOT NULL;",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }
}
