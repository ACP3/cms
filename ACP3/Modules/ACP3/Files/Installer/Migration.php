<?php

namespace ACP3\Modules\ACP3\Files\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Files\Installer
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
                "ALTER TABLE `{pre}files` CHANGE `link_title` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"extensions/feeds\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "files/", "files/index/") WHERE `uri` LIKE "files/%";',
            ],
            34 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/list/", "files/index/index/") WHERE `uri` LIKE "files/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/files/", "files/index/files/") WHERE `uri` LIKE "files/files/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "files/details/", "files/index/details/") WHERE `uri` LIKE "files/details/%";' : '',
            ],
            35 => [
                "ALTER TABLE `{pre}files` CHANGE `title` `title` VARCHAR(255) NOT NULL;",
            ],
            36 => [
                "ALTER TABLE `{pre}files` ENGINE = InnoDB",
            ],
            37 => [
                "ALTER TABLE `{pre}files` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE CASCADE"
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
