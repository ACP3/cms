<?php

namespace ACP3\Modules\ACP3\Seo;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Seo
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'seo';
    const SCHEMA_VERSION = 4;

    /**
     * @inheritdoc
     */
    public function removeResources()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE IF NOT EXISTS `{pre}seo` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uri` VARCHAR(255) NOT NULL,
                `alias` VARCHAR(100) NOT NULL,
                `keywords` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `robots` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), UNIQUE(`uri`), INDEX (`alias`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'meta_description' => '',
            'meta_keywords' => '',
            'mod_rewrite' => false,
            'robots' => 1,
            'title' => ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeSettings()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeFromModulesTable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            2 => [
                'DELETE FROM `{pre}settings` WHERE `module_id` = ' . $this->getModuleId() . ' AND `name` LIKE "seo_%";',
                'UPDATE `{pre}settings` SET `module_id` = ' . $this->getModuleId() . ' WHERE `module_id` = (SELECT `id` FROM `{pre}modules` WHERE `name` = "system") AND `name` LIKE "seo_%";'
            ],
            3 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'admin', 'index', 'settings', '', 7);",
            ],
            4 => [
                'UPDATE `{pre}settings` SET `name` = SUBSTRING(`name`, 5) WHERE `module_id` = ' . $this->getModuleId() . ' AND `name` LIKE "seo_%";',
            ]
        ];
    }
}
