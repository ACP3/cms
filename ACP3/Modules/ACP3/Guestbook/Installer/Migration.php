<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Installer;

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
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "guestbook/", "guestbook/index/") WHERE `uri` LIKE "guestbook/%";',
            ],
            32 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "guestbook/list/", "guestbook/index/index/") WHERE `uri` LIKE "guestbook/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "guestbook/create/", "guestbook/index/create/") WHERE `uri` LIKE "guestbook/create/%";' : '',
            ],
            33 => [
                'ALTER TABLE `{pre}guestbook` ENGINE = InnoDB',
            ],
            34 => [
                'ALTER TABLE `{pre}guestbook` CHANGE `user_id` `user_id` INT(10) UNSIGNED',
                'UPDATE `{pre}guestbook` SET `user_id` = NULL WHERE `user_id` = 0',
                'ALTER TABLE `{pre}guestbook` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL',
            ],
            35 => [
                'ALTER TABLE `{pre}guestbook` CONVERT TO {charset};',
                'ALTER TABLE `{pre}guestbook` MODIFY COLUMN `ip` VARCHAR(40) {charset} NOT NULL;',
                'ALTER TABLE `{pre}guestbook` MODIFY COLUMN `name` VARCHAR(20) {charset} NOT NULL;',
                'ALTER TABLE `{pre}guestbook` MODIFY COLUMN `message` TEXT {charset} NOT NULL;',
                'ALTER TABLE `{pre}guestbook` MODIFY COLUMN `website` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}guestbook` MODIFY COLUMN `mail` VARCHAR(120) {charset} NOT NULL;',
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
