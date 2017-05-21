<?php

namespace ACP3\Modules\ACP3\Emoticons\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Emoticons\Installer
 */
class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    const MODULE_NAME = 'emoticons';

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}emoticons` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(10) NOT NULL,
                `description` VARCHAR(15) NOT NULL,
                `img` VARCHAR(40) NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE IF EXISTS `{pre}emoticons`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'width' => 32,
            'height' => 32,
            'filesize' => 10240,
        ];
    }

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'admin' => [
                'index' => [
                    'index' => PrivilegeEnum::ADMIN_VIEW,
                    'create' => PrivilegeEnum::ADMIN_CREATE,
                    'edit' => PrivilegeEnum::ADMIN_EDIT,
                    'delete' => PrivilegeEnum::ADMIN_DELETE,
                    'settings' => PrivilegeEnum::ADMIN_SETTINGS
                ]
            ],
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return static::MODULE_NAME;
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 32;
    }
}
