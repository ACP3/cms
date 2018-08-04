<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\Application\BootstrapInterface;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use Symfony\Component\Yaml\Dumper;

class Migration extends Modules\Installer\AbstractMigration
{
    /**
     * @var \ACP3\Core\Modules\SchemaInstaller
     */
    private $schemaInstaller;
    /**
     * @var \ACP3\Core\Modules\AclInstaller
     */
    private $aclInstaller;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    /**
     * @var Modules\Installer\SchemaInterface|null
     */
    private $seoSchema;
    /**
     * @var Modules\Installer\SchemaInterface|null
     */
    private $minifySchema;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;

    public function __construct(
        Modules\SchemaHelper $schemaHelper,
        ApplicationPath $appPath,
        Modules\SchemaInstaller $schemaInstaller,
        Modules\AclInstaller $aclInstaller,
        Modules $modules,
        SettingsInterface $settings
    ) {
        parent::__construct($schemaHelper);
        $this->schemaInstaller = $schemaInstaller;
        $this->aclInstaller = $aclInstaller;
        $this->modules = $modules;
        $this->settings = $settings;
        $this->appPath = $appPath;
    }

    /**
     * @param Modules\Installer\SchemaInterface|null $seoSchema
     */
    public function setSeoInstallerSchema(?Modules\Installer\SchemaInterface $seoSchema)
    {
        $this->seoSchema = $seoSchema;
    }

    /**
     * @param Modules\Installer\SchemaInterface|null $minifySchema
     */
    public function setMinifyInstallerSchema(?Modules\Installer\SchemaInterface $minifySchema)
    {
        $this->minifySchema = $minifySchema;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'extra_css', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'extra_js', '');",
            ],
            32 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'icons_path', 'libraries/crystal_project/');",
            ],
            33 => [
                "UPDATE `{pre}acl_resources` SET `privilege_id` = 3 WHERE module_id = '{moduleId}' AND `page` = 'acp_maintenance';",
            ],
            34 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'functions', '', 1);",
            ],
            35 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
                "UPDATE `{pre}settings` SET `value` = \"4.0-dev\" WHERE `module_id` = '{moduleId}' AND `name` = 'version';",
            ],
            36 => [
                'ALTER TABLE `{pre}acl_resources` ADD COLUMN `area` VARCHAR(255) NOT NULL AFTER `module_id`;',
                'ALTER TABLE `{pre}acl_resources` ADD COLUMN `controller` VARCHAR(255) NOT NULL AFTER `area`;',
                'UPDATE `{pre}acl_resources` SET `area`="frontend";',
                'UPDATE `{pre}acl_resources` SET `area`="admin" WHERE `page` LIKE "acp_%";',
                'UPDATE `{pre}acl_resources` SET `area`="sidebar" WHERE `page` LIKE "sidebar%";',
            ],
            37 => [
                'UPDATE `{pre}acl_resources` SET `controller`="index";',
                'UPDATE `{pre}acl_resources` SET `page`=REPLACE(`page`, "acp_", "") WHERE `page` LIKE "acp_%";',
            ],
            38 => [
                'UPDATE `{pre}acl_resources` SET `page`="index" WHERE `page`="sidebar";',
            ],
            39 => [
                'UPDATE `{pre}acl_resources` SET `page`=REPLACE(`page`, "list", "index") WHERE `page` LIKE "list%";',
            ],
            40 => [
                "UPDATE `{pre}acl_resources` SET controller = 'maintenance' WHERE `module_id` = '{moduleId}' AND `page` = 'sql_export';",
                "UPDATE `{pre}acl_resources` SET controller = 'maintenance' WHERE `module_id` = '{moduleId}' AND `page` = 'sql_import';",
                "UPDATE `{pre}acl_resources` SET controller = 'maintenance' WHERE `module_id` = '{moduleId}' AND `page` = 'update_check';",
                "UPDATE `{pre}acl_resources` SET controller = 'maintenance', `page` = 'index' WHERE `module_id` = '{moduleId}' AND `page` = 'maintenance';",
            ],
            41 => [
                "UPDATE `{pre}acl_resources` SET controller = 'extensions' WHERE `module_id` = '{moduleId}' AND `page` = 'designs';",
                "UPDATE `{pre}acl_resources` SET controller = 'extensions' WHERE `module_id` = '{moduleId}' AND `page` = 'languages';",
                "UPDATE `{pre}acl_resources` SET controller = 'extensions' WHERE `module_id` = '{moduleId}' AND `page` = 'modules';",
                "UPDATE `{pre}acl_resources` SET controller = 'extensions', `page` = 'index' WHERE `module_id` = '{moduleId}' AND `page` = 'extensions';",
            ],
            42 => [
                "UPDATE `{pre}settings` SET `value` = 'de_DE' WHERE `module_id` = '{moduleId}' AND `name` = 'lang' AND `value` = 'de';",
                "UPDATE `{pre}settings` SET `value` = 'en_US' WHERE `module_id` = '{moduleId}' AND `name` = 'lang' AND `value` = 'en';",
            ],
            43 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'extensions' AND `page` = 'languages';",
            ],
            44 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'cache_driver', 'PhpFile');",
            ],
            45 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'seo_aliases';",
            ],
            46 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'icons_path';",
            ],
            47 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'extra_css';",
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'extra_js';",
            ],
            48 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'cache_driver';",
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` = 'version';",
            ],
            49 => [
                'ALTER TABLE `{pre}seo` DROP INDEX `PRIMARY`;',
                'ALTER TABLE `{pre}seo` ADD UNIQUE (uri);',
                'ALTER TABLE `{pre}seo` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;',
            ],
            50 => [
                $this->migrateToVersion50(),
            ],
            51 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'maintenance', 'cache', '', 7);",
            ],
            52 => [
                $this->migrationToVersion52(),
            ],
            53 => [
                $this->migrateToVersion53(),
            ],
            54 => [
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.ckeditor' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'CKEditor';",
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.textarea' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'Textarea';",
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.tinymce' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'TinyMCE';",
            ],
            55 => [
                'ALTER TABLE `{pre}modules` ENGINE = InnoDB',
                'ALTER TABLE `{pre}sessions` ENGINE = InnoDB',
                'ALTER TABLE `{pre}settings` ENGINE = InnoDB',
            ],
            56 => [
                'DELETE FROM `{pre}settings` WHERE `module_id` NOT IN (SELECT `id` FROM `{pre}modules`);',
                'ALTER TABLE `{pre}settings` CHANGE `module_id` `module_id` INT(10) UNSIGNED NOT NULL',
                'ALTER TABLE `{pre}settings` ADD INDEX (`module_id`)',
                'ALTER TABLE `{pre}settings` ADD FOREIGN KEY (`module_id`) REFERENCES `{pre}modules` (`id`) ON DELETE CASCADE',
            ],
            57 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'maintenance' AND `page` ='sql_export';",
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'maintenance' AND `page` ='sql_import';",
            ],
            58 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'mod_rewrite', '0');",
            ],
            59 => [
                "UPDATE `{pre}settings` SET `name` = 'cache_lifetime' WHERE `module_id` = {moduleId} AND `name` = 'cache_minify';",
            ],
            60 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'security_secret', '" . \uniqid(
                    \mt_rand(),
                    true
                ) . "');",
            ],
            61 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'cache_vary_user', 0);",
            ],
            62 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'page_cache_is_valid', 1);",
            ],
            63 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'page_cache_is_enabled', 1);",
            ],
            64 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'site_title', '');",
                $this->migrateToVersion64(),
            ],
            65 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'page_cache_purge_mode', 1);",
            ],
            66 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'cookie_consent_is_enabled', 0);",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'cookie_consent_text', '');",
            ],
            67 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'site_subtitle', '');",
            ],
            68 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'site_subtitle_homepage_mode', '0');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'site_subtitle_mode', '1');",
            ],
            69 => [
                "UPDATE `{pre}acl_resources` SET `page` = 'settings' WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'configuration';",
            ],
            70 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'update_last_check', '0');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'update_new_version', '" . BootstrapInterface::VERSION . "');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'update_new_version_url', '');",
            ],
            71 => [
                'ALTER TABLE `{pre}modules` CONVERT TO {charset};',
                'ALTER TABLE `{pre}modules` MODIFY COLUMN `name` VARCHAR(100) {charset} NOT NULL;',
                'ALTER TABLE `{pre}sessions` CONVERT TO {charset};',
                'ALTER TABLE `{pre}sessions` MODIFY COLUMN `session_id` VARCHAR(32) {charset} NOT NULL;',
                'ALTER TABLE `{pre}sessions` MODIFY COLUMN `session_data` TEXT {charset} NOT NULL;',
                'ALTER TABLE `{pre}settings` CONVERT TO {charset};',
                'ALTER TABLE `{pre}settings` MODIFY COLUMN `name` VARCHAR(40) {charset} NOT NULL;',
                'ALTER TABLE `{pre}settings` MODIFY COLUMN `value` TEXT {charset} NOT NULL;',
                "ALTER DATABASE `{$this->schemaHelper->getDb()->getDatabase()}` {charset};",
            ],
            72 => [
                $this->migrateToVersion72(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function renameModule()
    {
        return [];
    }

    /**
     * @return \Closure
     */
    protected function migrateToVersion50()
    {
        return function () {
            $result = true;
            if ($this->seoSchema !== null &&
                $this->schemaHelper->getSystemModuleRepository()->moduleExists('seo') === false
            ) {
                $result = $this->schemaInstaller->install($this->seoSchema);
                $aclResult = $this->aclInstaller->install($this->seoSchema);

                if ($result === true && $aclResult === true) {
                    $seoModuleId = $this->schemaHelper->getDb()->fetchColumn(
                        "SELECT `id` FROM `{$this->schemaHelper->getDb()->getPrefixedTableName('modules')}` WHERE `name` = 'seo'"
                    );
                    $result = $this->schemaHelper->executeSqlQueries([
                        "DELETE FROM `{pre}settings` WHERE `module_id` = {$seoModuleId};",
                        "UPDATE `{pre}settings` SET `module_id` = {$seoModuleId}, `name` = SUBSTRING(`name`, 5) WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
                    ], 'system');
                }
            }

            return $result;
        };
    }

    /**
     * @return \Closure
     */
    protected function migrationToVersion52()
    {
        return function () {
            $result = $aclResult = true;
            if ($this->minifySchema !== null &&
                $this->schemaHelper->getSystemModuleRepository()->moduleExists('minify') === false
            ) {
                $result = $this->schemaInstaller->install($this->minifySchema);
                $aclResult = $this->aclInstaller->install($this->minifySchema);
            }

            return $result && $aclResult;
        };
    }

    /**
     * @return \Closure
     */
    protected function migrateToVersion53()
    {
        return function () {
            $result = true;
            if ($this->schemaHelper->getSystemModuleRepository()->moduleExists('minify') === true) {
                $minifyModuleId = $this->schemaHelper->getDb()->fetchColumn("SELECT `id` FROM `{$this->schemaHelper->getDb()->getPrefixedTableName('modules')}` WHERE `name` = 'minify'");
                $result = $this->schemaHelper->executeSqlQueries([
                    "DELETE FROM `{pre}acl_resources` WHERE `module_id` = {$minifyModuleId};",
                    "DELETE FROM `{pre}modules` WHERE `id` = {$minifyModuleId};",
                ]);
            }

            return $result;
        };
    }

    /**
     * @return \Closure
     */
    protected function migrateToVersion64()
    {
        return function () {
            $result = true;

            if ($this->modules->isInstalled('seo')) {
                $seoSettings = $this->settings->getSettings('seo');

                if (isset($seoSettings['title'])) {
                    return $this->settings->saveSettings(
                        ['site_title' => $seoSettings['title']],
                        Schema::MODULE_NAME
                    );
                }
            }

            return $result;
        };
    }

    protected function migrateToVersion72()
    {
        return function () {
            $configFilePath = $this->appPath->getAppDir() . 'config.yml';
            $container = $this->schemaHelper->getContainer();

            $configParams = [
                'parameters' => [
                    'db_host' => $container->getParameter('db_host'),
                    'db_name' => $container->getParameter('db_name'),
                    'db_table_prefix' => $container->getParameter('db_table_prefix'),
                    'db_password' => $container->getParameter('db_password'),
                    'db_user' => $container->getParameter('db_user'),
                    'db_driver' => $container->getParameter('db_driver'),
                    'db_charset' => 'utf8mb4',
                ],
            ];

            if (\is_writable($configFilePath) === true) {
                \ksort($configParams);

                $dumper = new Dumper();
                $yaml = $dumper->dump($configParams);

                return \file_put_contents($configFilePath, $yaml, LOCK_EX) !== false;
            }

            return false;
        };
    }
}
