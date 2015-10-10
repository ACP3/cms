<?php

namespace ACP3\Modules\ACP3\Guestbook\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Guestbook\Installer
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
        return 'guestbook';
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
            "CREATE TABLE `{pre}guestbook` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` DATETIME NOT NULL,
                `ip` VARCHAR(40) NOT NULL,
                `name` VARCHAR(20) NOT NULL,
                `user_id` INT(10) UNSIGNED NOT NULL,
                `message` TEXT NOT NULL,
                `website` VARCHAR(120) NOT NULL,
                `mail` VARCHAR(120) NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), INDEX `foreign_user_id` (`user_id`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return ["DROP TABLE `{pre}guestbook`;"];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'dateformat' => 'long',
            'notify' => 0,
            'notify_email' => '',
            'emoticons' => 1,
            'newsletter_integration' => 0,
            'overlay' => 1
        ];
    }
}
