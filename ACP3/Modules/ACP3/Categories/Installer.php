<?php

namespace ACP3\Modules\ACP3\Categories;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\ACP3\Categories
 */
class Installer extends Modules\SchemaInstaller
{
    const MODULE_NAME = 'categories';
    const SCHEMA_VERSION = 32;

    /**
     * @inheritdoc
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}categories` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(120) NOT NULL,
                `picture` VARCHAR(120) NOT NULL,
                `description` VARCHAR(120) NOT NULL,
                `module_id` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @inheritdoc
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}categories`;"];
    }

    /**
     * @inheritdoc
     */
    public function settings()
    {
        return [
            'width' => 100,
            'height' => 50,
            'filesize' => 40960
        ];
    }

    /**
     * @inheritdoc
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "ALTER TABLE `{pre}categories` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = " . $this->getModuleId() . " AND `page` = \"functions\";",
            ]
        ];
    }
}
