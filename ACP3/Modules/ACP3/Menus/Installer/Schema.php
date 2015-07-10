<?php

namespace ACP3\Modules\ACP3\Menus\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Menus\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
     */
    public function specialResources()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'menus';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 33;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}menu_items` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mode` TINYINT(1) UNSIGNED NOT NULL,
                `block_id` INT(10) UNSIGNED NOT NULL,
                `root_id` INT(10) UNSIGNED NOT NULL,
                `parent_id` INT(10) UNSIGNED NOT NULL,
                `left_id` INT(10) UNSIGNED NOT NULL,
                `right_id` INT(10) UNSIGNED NOT NULL,
                `display` TINYINT(1) UNSIGNED NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                `uri` VARCHAR(120) NOT NULL,
                `target` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_block_id` (`block_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}menus` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `index_name` VARCHAR(10) NOT NULL,
                `title` VARCHAR(120) NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE `{pre}menus`;",
            "DROP TABLE `{pre}menu_items`;"
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [];
    }

}
