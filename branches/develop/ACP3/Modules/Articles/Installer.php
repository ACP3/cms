<?php

namespace ACP3\Modules\Articles;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Articles
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'articles';
    const SCHEMA_VERSION = 36;

    /**
     * @inheritdoc
     */
    public function renameModule()
    {
        return [
            31 => "UPDATE `{pre}modules` SET name = 'articles' WHERE name = 'static_pages';"
        ];
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}articles` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `start` DATETIME NOT NULL,
                `end` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}articles`;"];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "RENAME TABLE `{pre}static_pages` TO `{pre}articles`;",
                "UPDATE `{pre}seo` SET uri = REPLACE(uri, 'static_pages', 'articles') WHERE uri REGEXP '^(static_pages/list/id_[0-9]+/)$';",
                "UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'static_pages/list/id_', 'articles/list/id_') WHERE `text` REGEXP '(static_pages/list/id_[0-9]+/)';",
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET uri = REPLACE(uri, 'static_pages', 'articles') WHERE uri REGEXP '^(static_pages/list/id_[0-9]+/)$';" : ''
            ],
            32 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'details', '', 1);",
                "UPDATE `{pre}seo` SET uri = REPLACE(uri, '/list/', '/details/') WHERE uri REGEXP '^(articles/list/id_[0-9]+/)$';",
                "UPDATE `{pre}articles` SET `text` = REPLACE(`text`, 'articles/list/id_', 'articles/details/id_') WHERE `text` REGEXP '(articles/list/id_[0-9]+/)';",
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? "UPDATE `{pre}menu_items` SET uri = REPLACE(uri, '/list/', '/details/') WHERE uri REGEXP '^(articles/list/id_[0-9]+/)$';" : ''
            ],
            33 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"extensions/search\";",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ],
            34 => [
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "articles/", "articles/index/") WHERE uri LIKE "articles/%";',
            ],
            35 => [
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "articles/list/", "articles/index/index/") WHERE uri LIKE "articles/list/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "articles/details/", "articles/index/details/") WHERE uri LIKE "articles/details/%";' : '',
            ],
            36 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar', 'index', 'index', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar', 'index', 'single', '', 1);",
            ]
        ];
    }
}
