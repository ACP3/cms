<?php
namespace ACP3\Modules\ACP3\Articles\Installer;

use ACP3\Core\Modules\Installer\AbstractMigration;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Articles\Installer
 */
class Migration extends AbstractMigration
{

    /**
     * Aktualisiert die Tabellen und Einstellungen eines Moduls auf eine neue Version
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "RENAME TABLE `{pre}static_pages` TO `{pre}articles`;",
                "UPDATE `{pre}seo` SET `uri` = REPLACE(`uri`, 'static_pages', 'articles') WHERE `uri` REGEXP '^(static_pages/list/id_[0-9]+/)$';",
                "UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'static_pages/list/id_', 'articles/list/id_') WHERE `text` REGEXP '(static_pages/list/id_[0-9]+/)';",
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET `uri` = REPLACE(`uri`, 'static_pages', 'articles') WHERE `uri` REGEXP '^(static_pages/list/id_[0-9]+/)$';" : ''
            ],
            32 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', {$this->schemaHelper->getModuleId()}, 'details', '', 1);",
                "UPDATE `{pre}seo` SET `uri` = REPLACE(`uri`, '/list/', '/details/') WHERE `uri` REGEXP '^(articles/list/id_[0-9]+/)$';",
                "UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'articles/list/id_', 'articles/details/id_') WHERE `text` REGEXP '(articles/list/id_[0-9]+/)';",
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET `uri` = REPLACE(`uri`, '/list/', '/details/') WHERE `uri` REGEXP '^(articles/list/id_[0-9]+/)$';" : ''
            ],
            33 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = {$this->schemaHelper->getModuleId()} AND `page` = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = {$this->schemaHelper->getModuleId()} AND `page` = \"functions\";",
            ],
            34 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "articles/", "articles/index/") WHERE `uri` LIKE "articles/%";',
            ],
            35 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "articles/list/", "articles/index/index/") WHERE `uri` LIKE "articles/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "articles/details/", "articles/index/details/") WHERE `uri` LIKE "articles/details/%";' : '',
            ],
            36 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', {$this->schemaHelper->getModuleId()}, 'sidebar', 'index', 'index', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', {$this->schemaHelper->getModuleId()}, 'sidebar', 'index', 'single', '', 1);",
            ]
        ];
    }

    /**
     * Methodenstub zum Umbenennen eines Moduls
     *
     * @return array
     */
    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET `name` = 'articles' WHERE `name` = 'static_pages';"
        ];
    }
}