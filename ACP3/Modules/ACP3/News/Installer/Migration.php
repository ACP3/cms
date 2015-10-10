<?php

namespace ACP3\Modules\ACP3\News\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\News\Installer
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
                "ALTER TABLE `{pre}news` CHANGE `headline` `title` VARCHAR(120) {CHARSET} NOT NULL",
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
                "ALTER TABLE `{pre}news` ENGINE = InnoDB",
            ],
            37 => [
                "ALTER TABLE `{pre}news` ADD FOREIGN KEY (`category_id`) REFERENCES `{pre}categories` (`id`) ON DELETE CASCADE"
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
