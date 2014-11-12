<?php

namespace ACP3\Modules\Categories;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Categories
 */
class Installer extends Modules\AbstractInstaller
{

    const MODULE_NAME = 'categories';
    const SCHEMA_VERSION = 32;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return array(
            "CREATE TABLE `{pre}categories` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(120) NOT NULL,
                `picture` VARCHAR(120) NOT NULL,
                `description` VARCHAR(120) NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {engine} {charset};"
        );
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return array("DROP TABLE `{pre}categories`;");
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return array(
            'width' => 100,
            'height' => 50,
            'filesize' => 40960
        );
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return array(
            31 => array(
                "ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {charset} NOT NULL;",
            ),
            32 => array(
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND page = \"functions\";",
            )
        );
    }

}