<?php

namespace ACP3\Modules\ACP3\Guestbook\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Guestbook\Installer
 */
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
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "guestbook/", "guestbook/index/") WHERE `uri` LIKE "guestbook/%";',
            ],
            32 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "guestbook/list/", "guestbook/index/index/") WHERE `uri` LIKE "guestbook/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "guestbook/create/", "guestbook/index/create/") WHERE `uri` LIKE "guestbook/create/%";' : '',
            ],
            33 => [
                "ALTER TABLE `{pre}guestbook` ENGINE = InnoDB",
            ],
            34 => [
                "ALTER TABLE `{pre}guestbook` CHANGE `user_id` `user_id` INT(10) UNSIGNED",
                "UPDATE `{pre}guestbook` SET `user_id` = NULL WHERE `user_id` = 0",
                "ALTER TABLE `{pre}guestbook` ADD FOREIGN KEY (`user_id`) REFERENCES `{pre}users` (`id`) ON DELETE SET NULL"
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
