<?php

namespace ACP3\Modules\ACP3\Seo\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\Seo\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    const MODULE_NAME = 'seo';

    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'Admin' => [
                'Index' => [
                    'sitemap' => 7,
                    'suggest' => 4,
                ],
            ]
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
        return 11;
    }

    /**
     * @return array
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
     * @return array
     */
    public function removeTables()
    {
        return [
            "DROP TABLE IF EXISTS `{pre}seo`;",
        ];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'meta_description' => '',
            'meta_keywords' => '',
            'robots' => 1,
            'sitemap_is_enabled' => 0,
            'sitemap_save_mode' => 2,
            'sitemap_separate' => 0
        ];
    }
}
