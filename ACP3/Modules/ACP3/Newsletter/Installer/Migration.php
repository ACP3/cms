<?php

namespace ACP3\Modules\ACP3\Newsletter\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Newsletter\Installer
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
                "RENAME TABLE `{pre}newsletter_archive` TO `{pre}newsletters`;",
                "ALTER TABLE `{pre}newsletters` CHANGE `subject` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'archive', '', 1);",
            ],
            33 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'sidebar', '', 1);",
            ],
            34 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = \"functions\";",
            ],
            35 => [
                "DELETE FROM `{pre}acl_resources` WHERE module_id = '{moduleId}' AND `page` = 'archive';",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'list_archive', '', 1);",
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'details', '', 1);",
            ],
            36 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'html', '1');",
            ],
            37 => [
                "ALTER TABLE `{pre}newsletters` ADD `html` TINYINT(1) NOT NULL;",
            ],
            38 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "newsletter/", "newsletter/index/") WHERE `uri` LIKE "newsletter/%";',
            ],
            39 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'accounts' WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_account%';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_accounts', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_accounts';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_account', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_account';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'archive', `page` = 'index' WHERE `module_id` = '{moduleId}' AND `page` = 'index_archive';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'archive' WHERE `module_id` = '{moduleId}' AND `page` = 'details';",
            ],
            40 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'accounts' WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `page` = 'activate';",
            ],
            41 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "newsletter/list/", "newsletter/index/index/") WHERE `uri` LIKE "newsletter/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "newsletter/list_archive/", "newsletter/archive/index/") WHERE `uri` LIKE "newsletter/list_archive/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "newsletter/details/", "newsletter/archive/details/") WHERE `uri` LIKE "newsletter/details/%";' : '',
            ],
            42 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'frontend', `controller` = 'archive', `page` = 'index' WHERE `module_id` = '{moduleId}' AND `page` = 'list_archive';",
                "UPDATE `{pre}acl_resources` SET `area` = 'frontend' WHERE `module_id` = '{moduleId}' AND `controller` = 'archive' AND `page` = 'details';",
            ],
            43 => [
                'CREATE TABLE `{pre}newsletter_queue` (
                    `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                    `newsletter_id` INT(10) UNSIGNED NOT NULL,
                    INDEX (`newsletter_account_id`), INDEX (`newsletter_id`)
                ) {ENGINE} {CHARSET};'
            ],
            44 => [
                "ALTER TABLE `{pre}newsletter_accounts` CHANGE `mail` `mail` VARCHAR(255) NOT NULL;",
                "ALTER TABLE `{pre}newsletter_accounts` CHANGE `hash` `hash` VARCHAR(128) NOT NULL;",
                "ALTER TABLE `{pre}newsletter_accounts` ADD `salutation` TINYINT(1) NOT NULL AFTER `mail`;",
                "ALTER TABLE `{pre}newsletter_accounts` ADD `first_name` VARCHAR(255) NOT NULL AFTER `salutation`;",
                "ALTER TABLE `{pre}newsletter_accounts` ADD `last_name` VARCHAR(255) NOT NULL AFTER `first_name`;",
                "ALTER TABLE `{pre}newsletter_accounts` ADD `status` TINYINT(1) NOT NULL AFTER `hash`;",
                "UPDATE `{pre}newsletter_accounts` SET `status` = 1 WHERE `hash` = '' OR `hash` IS NULL;"
            ],
            45 => [
                "CREATE TABLE `{pre}newsletter_account_history` (
                    `newsletter_account_id` INT(10) UNSIGNED NOT NULL,
                    `date` DATETIME NOT NULL,
                    `action` TINYINT(1) NOT NULL,
                    INDEX (`newsletter_account_id`)
                ) {ENGINE} {CHARSET};",
            ],
            46 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'frontend', 'index', 'unsubscribe', '', 1);",
            ],
            47 => [
                "ALTER TABLE `{pre}newsletter_accounts` ADD INDEX (`mail`);",
                "ALTER TABLE `{pre}newsletter_accounts` ADD INDEX (`hash`);",
            ],
            48 => [
                "ALTER TABLE `{pre}newsletter_accounts` ENGINE = InnoDB",
                "ALTER TABLE `{pre}newsletter_account_history` ENGINE = InnoDB",
                "ALTER TABLE `{pre}newsletter_queue` ENGINE = InnoDB",
                "ALTER TABLE `{pre}newsletters` ENGINE = InnoDB",
            ],
            49 => [
                "ALTER TABLE `{pre}newsletter_account_history` ADD FOREIGN KEY (`newsletter_account_id`) REFERENCES `{pre}newsletter_accounts` (`id`)",
                "ALTER TABLE `{pre}newsletter_queue` ADD UNIQUE KEY (`newsletter_account_id`, `newsletter_id`)",
                "ALTER TABLE `{pre}newsletter_queue` ADD FOREIGN KEY (`newsletter_account_id`) REFERENCES `{pre}newsletter_accounts` (`id`)",
                "ALTER TABLE `{pre}newsletter_queue` ADD FOREIGN KEY (`newsletter_id`) REFERENCES `{pre}newsletters` (`id`) ON DELETE CASCADE",
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
