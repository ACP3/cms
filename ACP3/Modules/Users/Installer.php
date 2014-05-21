<?php

namespace ACP3\Modules\Users;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'users';
    const SCHEMA_VERSION = 37;

    public function removeResources()
    {
        return true;
    }

    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}users` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `super_user` TINYINT(1) UNSIGNED NOT NULL,
                `nickname` VARCHAR(30) NOT NULL,
                `pwd` VARCHAR(53) NOT NULL,
                `login_errors` TINYINT(1) UNSIGNED NOT NULL,
                `realname` VARCHAR(80) NOT NULL,
                `gender` TINYINT(1) NOT NULL,
                `birthday` VARCHAR(10) NOT NULL,
                `birthday_display` TINYINT(1) UNSIGNED NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `mail_display` TINYINT(1) UNSIGNED NOT NULL,
                `website` VARCHAR(120) NOT NULL,
                `icq` VARCHAR(11) NOT NULL,
                `skype` VARCHAR(30) NOT NULL,
                `street` VARCHAR(120) NOT NULL,
                `house_number` VARCHAR(5) NOT NULL,
                `zip` VARCHAR(6) NOT NULL,
                `city` VARCHAR(120) NOT NULL,
                `address_display` TINYINT(1) UNSIGNED NOT NULL,
                `country` CHAR(2) NOT NULL,
                `country_display` TINYINT(1) UNSIGNED NOT NULL,
                `date_format_long` VARCHAR(30) NOT NULL,
                `date_format_short` VARCHAR(30) NOT NULL,
                `time_zone` VARCHAR(100) NOT NULL,
                `language` VARCHAR(10) NOT NULL,
                `entries` TINYINT(2) UNSIGNED NOT NULL,
                `draft` TEXT NOT NULL,
                `registration_date` DATETIME NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};"
        );
    }

    public function removeTables()
    {
        return array();
    }

    public function settings()
    {
        return array(
            'enable_registration' => 1,
            'entries_override' => 1,
            'language_override' => 1,
            'mail' => ''
        );
    }

    public function removeSettings()
    {
        return true;
    }

    public function removeFromModulesTable()
    {
        return true;
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'mail', '');",
            ),
            32 => array(
                "ALTER TABLE `{pre}users` DROP `msn`;",
                "UPDATE `{pre}users` SET mail = REVERSE(SUBSTRING(REVERSE(mail), 1 + LOCATE(':', REVERSE(mail))));",
                "UPDATE `{pre}users` SET realname = REVERSE(SUBSTRING(REVERSE(realname), 1 + LOCATE(':', REVERSE(realname))));",
                "UPDATE `{pre}users` SET gender = REVERSE(SUBSTRING(REVERSE(gender), 1 + LOCATE(':', REVERSE(gender))));",
                "UPDATE `{pre}users` SET birthday = REVERSE(SUBSTRING(REVERSE(birthday), 1 + LOCATE(':', REVERSE(birthday))));",
                "UPDATE `{pre}users` SET website = REVERSE(SUBSTRING(REVERSE(website), 1 + LOCATE(':', REVERSE(website))));",
                "UPDATE `{pre}users` SET icq = REVERSE(SUBSTRING(REVERSE(icq), 1 + LOCATE(':', REVERSE(icq))));",
                "UPDATE `{pre}users` SET skype = REVERSE(SUBSTRING(REVERSE(skype), 1 + LOCATE(':', REVERSE(skype))));",
                "ALTER TABLE `{pre}users` CHANGE `gender` `gender` TINYINT(1) UNSIGNED NOT NULL;",
                "ALTER TABLE `{pre}users` CHANGE `birthday` `birthday` VARCHAR(10) NOT NULL;",
                "ALTER TABLE `{pre}users` CHANGE `birthday_format` `birthday_display` TINYINT(1) UNSIGNED NOT NULL;",
                "ALTER TABLE `{pre}users` ADD `street` VARCHAR(120) NOT NULL AFTER `skype`;",
                "ALTER TABLE `{pre}users` ADD `house_number` VARCHAR(5) NOT NULL AFTER `street`;",
                "ALTER TABLE `{pre}users` ADD `zip` VARCHAR(5) NOT NULL AFTER `house_number`;",
                "ALTER TABLE `{pre}users` ADD `city` VARCHAR(120) NOT NULL AFTER `zip`;",
                "ALTER TABLE `{pre}users` ADD `country` CHAR(2) NOT NULL AFTER `city`;",
                "ALTER TABLE `{pre}users` ADD `address_display` TINYINT(1) UNSIGNED NOT NULL AFTER `city`;",
                "ALTER TABLE `{pre}users` ADD `country_display` TINYINT(1) UNSIGNED NOT NULL AFTER `country`;",
                "ALTER TABLE `{pre}users` ADD `mail_display` TINYINT(1) UNSIGNED NOT NULL AFTER `mail`;",
            ),
            33 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            ),
            34 => array(
                "ALTER TABLE `{pre}users` ADD `registration_date` DATETIME NOT NULL AFTER `draft`;",
                "UPDATE `{pre}users` SET registration_date = '" . gmdate('Y-m-d H:i:s') . "';"
            ),
            35 => array(
                "DELETE FROM `{pre}acl_resources` WHERE module_id =" . $this->getModuleId() . " AND page = 'sidebar';",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar_login', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'sidebar_user_menu', '', 1);",
            ),
            36 => array(
                'UPDATE `{pre}seo` SET uri=REPLACE(uri, "users/", "users/index/") WHERE uri LIKE "users/%";',
            ),
            37 => array(
                'UPDATE `{pre}acl_resources` SET controller = "account", page = "edit" WHERE `module_id` = ' . $this->getModuleId() . ' AND area = "frontend" AND page = "edit_profile";',
                'UPDATE `{pre}acl_resources` SET controller = "account", page = "settings" WHERE `module_id` = ' . $this->getModuleId() . ' AND area = "frontend" AND page = "edit_settings";',
                'UPDATE `{pre}acl_resources` SET controller = "account", page = "index" WHERE `module_id` = ' . $this->getModuleId() . ' AND area = "frontend" AND page = "home";',
            )
        );
    }

}