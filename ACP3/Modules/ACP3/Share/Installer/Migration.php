<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Installer;

use ACP3\Core\Modules;

class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function schemaUpdates()
    {
        return [
            2 => [
                'ALTER TABLE `{pre}share` ADD COLUMN `ratings_active` TINYINT(1) UNSIGNED NOT NULL AFTER `services`;',
                'CREATE TABLE IF NOT EXISTS `{pre}share_ratings` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `stars` TINYINT(1) UNSIGNED NOT NULL,
                    `ip` VARCHAR(40) NOT NULL,
                    `share_id` INT(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`id`),
                    INDEX(`share_id`),
                    FOREIGN KEY (`share_id`) REFERENCES `{pre}share` (`id`) ON DELETE CASCADE
                ) {ENGINE} {CHARSET};',
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'frontend', 'index', 'rate', '', 1);",
            ],
            3 => [
                "ALTER TABLE `{pre}share` MODIFY COLUMN `uri` VARCHAR(191) NOT NULL;",
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function renameModule()
    {
        return [];
    }
}
