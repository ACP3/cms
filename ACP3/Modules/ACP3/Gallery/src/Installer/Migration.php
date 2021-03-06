<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Installer;

use ACP3\Core\Modules;

class Migration extends Modules\Installer\AbstractMigration
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
                'ALTER TABLE `{pre}gallery` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;',
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "gallery/", "gallery/index/") WHERE `uri` LIKE "gallery/%";',
            ],
            34 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'pictures' WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_picture';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_picture', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_picture';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'pictures' WHERE `module_id` = '{moduleId}' AND `page` = 'order';",
            ],
            35 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/list/", "gallery/index/index/") WHERE `uri` LIKE "gallery/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/pics/", "gallery/index/pics/") WHERE `uri` LIKE "gallery/pics/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/details/", "gallery/index/details/") WHERE `uri` LIKE "gallery/details/%";' : '',
            ],
            36 => [
                'ALTER TABLE `{pre}gallery` ENGINE = InnoDB',
                'ALTER TABLE `{pre}gallery_pictures` ENGINE = InnoDB',
            ],
            37 => [
                'ALTER TABLE `{pre}gallery_pictures` ADD FOREIGN KEY (`gallery_id`) REFERENCES `{pre}gallery` (`id`) ON DELETE CASCADE',
            ],
            38 => [
                'ALTER TABLE `{pre}gallery` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}gallery` ADD INDEX (`user_id`)',
                'UPDATE `{pre}gallery` SET `user_id` = NULL WHERE `user_id` = 0',
                'ALTER TABLE `{pre}gallery` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
            ],
            39 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = {moduleId} AND name = 'filesize';",
                "DELETE FROM `{pre}settings` WHERE `module_id` = {moduleId} AND name = 'maxheight';",
                "DELETE FROM `{pre}settings` WHERE `module_id` = {moduleId} AND name = 'maxwidth';",
            ],
            40 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'widget' WHERE `module_id` = '{moduleId}' AND `area` = 'sidebar';",
            ],
            41 => [
                'ALTER TABLE `{pre}gallery` ADD COLUMN `updated_at` DATETIME NOT NULL AFTER `end`;',
                'UPDATE `{pre}gallery` SET `updated_at` = `start`;',
            ],
            42 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'pictures', 'index', '', 3);",
            ],
            43 => [
                'ALTER TABLE `{pre}gallery` MODIFY COLUMN `title` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}gallery` CONVERT TO {charset};',
                'ALTER TABLE `{pre}gallery_pictures` MODIFY COLUMN `file` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}gallery_pictures` MODIFY COLUMN `description` TEXT {charset} NOT NULL;',
                'ALTER TABLE `{pre}gallery_pictures` CONVERT TO {charset};',
            ],
            44 => [
                'ALTER TABLE `{pre}gallery_pictures` ADD COLUMN `title` VARCHAR(120) NOT NULL AFTER `file`;',
            ],
            45 => [
                'ALTER TABLE `{pre}gallery` ADD COLUMN `description` TEXT NOT NULL AFTER `title`;',
            ],
            46 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'widget', 'index', 'pictures', '', 1);",
            ],
            47 => [
                'ALTER TABLE `{pre}gallery` ADD COLUMN `active` TINYINT(1) NOT NULL AFTER `id`;',
                'UPDATE `{pre}gallery` SET `active` = 1',
            ],
            48 => [
                'ALTER TABLE `{pre}gallery_pictures` DROP COLUMN `comments`;',
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'comments';",
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
