<?php

namespace ACP3\Modules\Seo;

use ACP3\Core\Modules;

/**
 * Class Installer
 * @package ACP3\Modules\Seo
 */
class Installer extends Modules\AbstractInstaller
{
    const MODULE_NAME = 'seo';
    const SCHEMA_VERSION = 1;

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
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `uri` varchar(255) NOT NULL,
                `alias` varchar(100) NOT NULL,
                `keywords` varchar(255) NOT NULL,
                `description` varchar(255) NOT NULL,
                `robots` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`), UNIQUE(`uri`), INDEX (`alias`)
            ) {engine} {charset};"
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
            'seo_meta_description' => '',
            'seo_meta_keywords' => '',
            'seo_mod_rewrite' => false,
            'seo_robots' => 1,
            'seo_title' => ''
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
        return true;
    }
}
