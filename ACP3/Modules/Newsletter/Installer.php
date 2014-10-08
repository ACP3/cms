<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core\Modules;
use ACP3\Modules\System;
use ACP3\Modules\Permissions;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'newsletter';
    const SCHEMA_VERSION = 43;

    /**
     * @var array
     */
    protected $specialResources = array(
        'Admin' => array(
            'Index' => array(
                'send' => 4
            )
        )
    );

    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}newsletter_accounts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mail` VARCHAR(120) NOT NULL,
                `hash` VARCHAR(32) NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}newsletter_queue` (
                `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                `newsletter_id` INT(10) UNSIGNED NOT NULL,
                INDEX (`newsletter_account_id`), INDEX (`newsletter_id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}newsletters` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
                `html` TINYINT(1) NOT NULL,
                `status` TINYINT(1) UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};"
        );
    }

    public function removeTables()
    {
        return array(
            "DROP TABLE `{pre}newsletter_accounts`;",
            "DROP TABLE `{pre}newsletters`;"
        );
    }

    public function settings()
    {
        return array(
            'mail' => '',
            'mailsig' => '',
            'html' => 1
        );
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "RENAME TABLE `{pre}newsletter_archive` TO `{pre}newsletters`;",
                "ALTER TABLE `{pre}newsletters` CHANGE `subject` `title` VARCHAR(120) {charset} NOT NULL;",
            ),
            32 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'archive', '', 1);",
            ),
            33 => array(
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
            ),
            34 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ),
            35 => array(
                "DELETE FROM `{pre}acl_resources` WHERE module_id = '" . $this->getModuleId() . "' AND page = 'archive';",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'list_archive', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'details', '', 1);",
            ),
            36 => array(
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'html', '1');",
            ),
            37 => array(
                "ALTER TABLE `{pre}newsletters` ADD `html` TINYINT(1) NOT NULL;",
            ),
            38 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "newsletter/", "newsletter/index/") WHERE uri LIKE "newsletter/%";',
            ),
            39 => array(
                'UPDATE `{pre}acl_resources` SET controller = "accounts" WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_account%";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_accounts", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_accounts";',
                'UPDATE `{pre}acl_resources` SET page = REPLACE(page, "_account", "") WHERE `module_id` = ' . $this->getModuleId() . ' AND page LIKE "%_account";',
                'UPDATE `{pre}acl_resources` SET controller = "archive", page = "index" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "index_archive";',
                'UPDATE `{pre}acl_resources` SET controller = "archive" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "details";',
            ),
            40 => array(
                'UPDATE `{pre}acl_resources` SET controller = "accounts" WHERE `module_id` = ' . $this->getModuleId() . ' AND area = "admin" AND page = "activate";',
            ),
            41 => array(
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "newsletter/list/", "newsletter/index/index/") WHERE uri LIKE "newsletter/list/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "newsletter/list_archive/", "newsletter/archive/index/") WHERE uri LIKE "newsletter/list_archive/%";' : '',
                $this->moduleIsInstalled('menus') || $this->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET uri=REPLACE(uri, "newsletter/details/", "newsletter/archive/details/") WHERE uri LIKE "newsletter/details/%";' : '',
            ),
            42 => array(
                'UPDATE `{pre}acl_resources` SET area = "frontend", controller = "archive", page = "index" WHERE `module_id` = ' . $this->getModuleId() . ' AND page = "list_archive";',
                'UPDATE `{pre}acl_resources` SET area = "frontend" WHERE `module_id` = ' . $this->getModuleId() . ' AND controller="archive" AND page = "details";',
            ),
            43 => array(
                'CREATE TABLE `{pre}newsletter_queue` (
                    `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                    `newsletter_id` INT(10) UNSIGNED NOT NULL,
                    INDEX (`newsletter_account_id`), INDEX (`newsletter_id`)
                ) {engine} {charset};'
            )
        );
    }

}
