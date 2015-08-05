<?php

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\Modules;

/**
 * Class Schema
 * @package ACP3\Modules\ACP3\System\Installer
 */
class Schema implements Modules\Installer\SchemaInterface
{
    /**
     * @return array
     */
    public function specialResources()
    {
        return [
            'Admin' => [
                'Extensions' => [
                    'index' => 7,
                    'designs' => 7,
                    'languages' => 7,
                    'modules' => 7,
                ],
                'Index' => [
                    'configuration' => 7,
                ],
                'Maintenance' => [
                    'cache' => 7,
                    'sql_export' => 7,
                    'sql_import' => 7,
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return 'system';
    }

    /**
     * @return int
     */
    public function getSchemaVersion()
    {
        return 54;
    }

    /**
     * @return array
     */
    public function createTables()
    {
        return [
            "CREATE TABLE `{pre}modules` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                `version` TINYINT(3) UNSIGNED NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}sessions` (
                `session_id` VARCHAR(32) NOT NULL,
                `session_starttime` INT(10) UNSIGNED NOT NULL,
                `session_data` TEXT NOT NULL,
                PRIMARY KEY (`session_id`)
            ) {ENGINE} {CHARSET};",
            "CREATE TABLE `{pre}settings` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `module_id` INT(10) NOT NULL,
                `name` VARCHAR(40) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id`), UNIQUE KEY (`module_id`,`name`)
            ) {ENGINE} {CHARSET};"
        ];
    }

    /**
     * @return array
     */
    public function removeTables()
    {
        return [];
    }

    /**
     * @return array
     */
    public function settings()
    {
        return [
            'cache_images' => true,
            'cache_minify' => 3600,
            'date_format_long' => '',
            'date_format_short' => '',
            'date_time_zone' => '',
            'design' => 'acp3',
            'entries' => '',
            'flood' => '20',
            'homepage' => 'news/index/index/',
            'lang' => '',
            'mailer_smtp_auth' => false,
            'mailer_smtp_host' => '',
            'mailer_smtp_password' => '',
            'mailer_smtp_port' => 25,
            'mailer_smtp_security' => '',
            'mailer_smtp_user' => '',
            'mailer_type' => 'mail',
            'maintenance_mode' => false,
            'maintenance_message' => '',
            'wysiwyg' => 'core.wysiwyg.ckeditor'
        ];
    }
}
