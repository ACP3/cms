<?php
namespace ACP3\Modules\ACP3\Categories\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Categories\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
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
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}categories`;"];
    }

    /**
     * @return array
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
        return 'categories';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 32;
    }
}
