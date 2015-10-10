<?php

namespace ACP3\Modules\ACP3\Users\Installer;

use ACP3\Core\Date;
use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Users\Installer
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
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'mail', '');",
            ],
            32 => [
                "ALTER TABLE `{pre}users` DROP `msn`;",
                "UPDATE `{pre}users` SET `mail` = REVERSE(SUBSTRING(REVERSE(`mail`), 1 + LOCATE(':', REVERSE(`mail`))));",
                "UPDATE `{pre}users` SET `realname` = REVERSE(SUBSTRING(REVERSE(`realname`), 1 + LOCATE(':', REVERSE(`realname`))));",
                "UPDATE `{pre}users` SET `gender` = REVERSE(SUBSTRING(REVERSE(`gender`), 1 + LOCATE(':', REVERSE(`gender`))));",
                "UPDATE `{pre}users` SET `birthday` = REVERSE(SUBSTRING(REVERSE(`birthday`), 1 + LOCATE(':', REVERSE(`birthday`))));",
                "UPDATE `{pre}users` SET `website` = REVERSE(SUBSTRING(REVERSE(`website`), 1 + LOCATE(':', REVERSE(`website`))));",
                "UPDATE `{pre}users` SET `icq` = REVERSE(SUBSTRING(REVERSE(`icq`), 1 + LOCATE(':', REVERSE(`icq`))));",
                "UPDATE `{pre}users` SET `skype` = REVERSE(SUBSTRING(REVERSE(`skype`), 1 + LOCATE(':', REVERSE(`skype`))));",
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
            ],
            33 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
            ],
            34 => [
                "ALTER TABLE `{pre}users` ADD `registration_date` DATETIME NOT NULL AFTER `draft`;",
                "UPDATE `{pre}users` SET `registration_date` = '" . gmdate(Date::DEFAULT_DATE_FORMAT_FULL) . "';"
            ],
            35 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'sidebar';",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'sidebar_login', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'sidebar_user_menu', '', 1);",
            ],
            36 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "users/", "users/index/") WHERE `uri` LIKE "users/%";',
            ],
            37 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'account', `page` = 'edit' WHERE `module_id` = '{moduleId}' AND `area` = 'frontend' AND `page` = 'edit_profile';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'account', `page` = 'settings' WHERE `module_id` = '{moduleId}' AND `area` = 'frontend' AND `page` = 'edit_settings';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'account', `page` = 'index' WHERE `module_id` = '{moduleId}' AND `area` = 'frontend' AND `page` = 'home';",
            ],
            38 => [
                'UPDATE `{pre}users` SET `language` = "de_DE" WHERE `language` = "de";',
                'UPDATE `{pre}users` SET `language` = "en_US" WHERE `language` = "en";',
            ],
            39 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/forgot_pwd/", "users/index/forgot_pwd/") WHERE `uri` LIKE "users/forgot_pwd/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/list/", "users/index/index/") WHERE `uri` LIKE "users/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/login/", "users/index/login/") WHERE `uri` LIKE "users/login/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/logout/", "users/index/logout/") WHERE `uri` LIKE "users/logout/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/register/", "users/index/register/") WHERE `uri` LIKE "users/register/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "users/view_profile/", "users/index/view_profile/") WHERE `uri` LIKE "users/view_profile/%";' : '',
            ],
            40 => [
                "ALTER TABLE `{pre}users` ADD `pwd_salt` VARCHAR(16) NOT NULL AFTER `pwd`;",
                "UPDATE `{pre}users` SET `pwd_salt` = SUBSTRING(`pwd`, 42), `pwd` = SUBSTRING(`pwd`, 1, 40);",
                "ALTER TABLE `{pre}users` ADD `remember_me_token` VARCHAR(128) NOT NULL AFTER `pwd_salt`;",
                "ALTER TABLE `{pre}users` CHANGE `pwd` `pwd` VARCHAR(128) NOT NULL;",
            ],
            41 => [
                "ALTER TABLE `{pre}users` ENGINE = InnoDB",
            ],
            42 => [
                "ALTER TABLE `{pre}users` CHANGE `id` `id` INT(10) UNSIGNED AUTO_INCREMENT",
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
