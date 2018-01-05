<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\Modules;

class Migration extends Modules\Installer\AbstractMigration
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
                'ALTER TABLE `{pre}news` CHANGE `headline` `title` VARCHAR(120) {CHARSET} NOT NULL',
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/feeds\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "news/", "news/index/") WHERE `uri` LIKE "news/%";',
            ],
            34 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "news/list/", "news/index/index/") WHERE `uri` LIKE "news/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "news/details/", "news/index/details/") WHERE `uri` LIKE "news/details/%";' : '',
            ],
            35 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'sidebar', 'index', 'latest', '', 1);",
            ],
            36 => [
                'ALTER TABLE `{pre}news` ENGINE = InnoDB',
            ],
            37 => [
                'ALTER TABLE `{pre}news` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE CASCADE',
            ],
            38 => [
                'ALTER TABLE `{pre}news` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}news` ADD INDEX (`user_id`)',
                'UPDATE `{pre}news` SET `user_id` = NULL WHERE `user_id` = 0',
                'ALTER TABLE `{pre}news` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
            ],
            39 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'widget' WHERE `module_id` = '{moduleId}' AND `area` = 'sidebar';",
            ],
            40 => [
                'ALTER TABLE `{pre}news` ADD COLUMN `updated_at` DATETIME NOT NULL AFTER `end`;',
                'UPDATE `{pre}news` SET `updated_at` = `start`;',
            ],
            41 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'duplicate', '', 4);",
            ],
            42 => [
                'ALTER TABLE `{pre}news` ADD COLUMN `active` TINYINT(1) UNSIGNED NOT NULL AFTER `id`;',
                'ALTER TABLE `{pre}news` ADD INDEX (`active`)',
                'UPDATE `{pre}news` SET `active` = 1;',
            ],
            43 => [
                'ALTER TABLE `{pre}news` CHANGE `category_id` `category_id` INT(10) UNSIGNED;',
                'ALTER TABLE `{pre}news` DROP FOREIGN KEY `{pre}news_ibfk_1`;',
                'ALTER TABLE `{pre}news` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE SET NULL',
            ],
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
