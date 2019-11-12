<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Installer;

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
                'ALTER TABLE `{pre}polls` CHANGE `question` `title` VARCHAR(120) {CHARSET} NOT NULL',
            ],
            32 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "polls/", "polls/index/") WHERE `uri` LIKE "polls/%";',
            ],
            33 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/list/", "polls/index/index/") WHERE `uri` LIKE "polls/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/vote/", "polls/index/vote/") WHERE `uri` LIKE "polls/vote/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "polls/result/", "polls/index/result/") WHERE `uri` LIKE "polls/result/%";' : '',
            ],
            34 => [
                'ALTER TABLE `{pre}polls` ENGINE = InnoDB',
                'ALTER TABLE `{pre}poll_answers` ENGINE = InnoDB',
                'ALTER TABLE `{pre}poll_votes` ENGINE = InnoDB',
            ],
            35 => [
                'ALTER TABLE `{pre}poll_answers` ADD FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE;',
                'ALTER TABLE `{pre}poll_votes` DROP INDEX `poll_id`',
                'ALTER TABLE `{pre}poll_votes` ADD INDEX (`poll_id`)',
                'ALTER TABLE `{pre}poll_votes` ADD INDEX (`answer_id`)',
                'ALTER TABLE `{pre}poll_votes` ADD INDEX (`user_id`)',
                'ALTER TABLE `{pre}poll_votes` ADD FOREIGN KEY (`poll_id`) REFERENCES `{pre}polls` (`id`) ON DELETE CASCADE;',
                'ALTER TABLE `{pre}poll_votes` ADD FOREIGN KEY (`answer_id`) REFERENCES `{pre}poll_answers` (`id`) ON DELETE CASCADE;',
            ],
            36 => [
                'ALTER TABLE `{pre}polls` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}polls` ADD INDEX (`user_id`)',
                'UPDATE `{pre}polls` SET `user_id` = NULL WHERE `user_id` = 0',
                'ALTER TABLE `{pre}polls` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
                'ALTER TABLE `{pre}poll_votes` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'ALTER TABLE `{pre}poll_votes` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
            ],
            37 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'widget' WHERE `module_id` = '{moduleId}' AND `area` = 'sidebar';",
            ],
            38 => [
                'ALTER TABLE `{pre}polls` ADD COLUMN `updated_at` DATETIME NOT NULL AFTER `end`;',
                'UPDATE `{pre}polls` SET `updated_at` = `start`;',
            ],
            39 => [
                'ALTER TABLE `{pre}polls` MODIFY COLUMN `title` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}polls` CONVERT TO {charset};',
                'ALTER TABLE `{pre}poll_answers` MODIFY COLUMN `text` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}poll_answers` CONVERT TO {charset};',
                'ALTER TABLE `{pre}poll_votes` MODIFY COLUMN `ip` VARCHAR(40) {charset} NOT NULL;',
                'ALTER TABLE `{pre}poll_votes` CONVERT TO {charset};',
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
