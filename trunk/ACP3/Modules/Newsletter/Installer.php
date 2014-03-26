<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core\Modules;

class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'newsletter';
    const SCHEMA_VERSION = 35;

    public function __construct()
    {
        $this->special_resources = array(
            'acp_send' => 4,
        );
    }

    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}newsletter_accounts` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mail` VARCHAR(120) NOT NULL,
                `hash` VARCHAR(32) NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};",
            "CREATE TABLE `{pre}newsletters` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `text` TEXT NOT NULL,
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
        );
    }

    public function schemaUpdates()
    {
        return array(
            31 => array(
                "RENAME TABLE `{pre}newsletter_archive` TO `{pre}newsletters`",
                "ALTER TABLE `{pre}newsletters` CHANGE `subject` `title` VARCHAR(120) {charset} NOT NULL",
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
            )
        );
    }

}
