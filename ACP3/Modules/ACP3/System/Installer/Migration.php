<?php

namespace ACP3\Modules\ACP3\System\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\System\Installer
 */
class Migration extends Modules\Installer\AbstractMigration
{
    /**
     * @inheritdoc
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
                $this->migrateToVersion50()
            ],
            51 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'maintenance', 'cache', '', 7);",
            ],
            52 => [
                $this->migrationToVersion52()
            ],
            53 => [
                $this->migrateToVersion53()
            ],
            54 => [
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.ckeditor' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'CKEditor';",
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.textarea' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'Textarea';",
                "UPDATE `{pre}settings` SET `value` = 'core.wysiwyg.tinymce' WHERE `module_id` = '{moduleId}' AND `name` = 'wysiwyg' AND `value` = 'TinyMCE';",
            ],
            55 => [
                "ALTER TABLE `{pre}modules` ENGINE = InnoDB",
                "ALTER TABLE `{pre}sessions` ENGINE = InnoDB",
                "ALTER TABLE `{pre}settings` ENGINE = InnoDB",
            ]
        ];
    }

    /**
     * @inheritdoc
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
            if ($this->schemaHelper->getContainer()->has('seo.installer.schema') &&
                $this->schemaHelper->getSystemModuleRepository()->moduleExists('seo') === false
            ) {
                $installer = $this->schemaHelper->getContainer()->get('core.modules.schemaInstaller');
                $moduleSchema = $this->schemaHelper->getContainer()->get('seo.installer.schema');
                $result = $installer->install($moduleSchema);
                $aclResult = $this->schemaHelper->getContainer()->get('core.modules.aclInstaller')->install($moduleSchema);

                if ($result === true && $aclResult === true) {
                    $seoModuleId = $this->schemaHelper->getDb()->fetchColumn(
                        "SELECT `id` FROM `{$this->schemaHelper->getDb()->getPrefixedTableName('modules')}` WHERE `name` = 'seo'"
                    );
                    $result = $this->schemaHelper->executeSqlQueries([
                        "DELETE FROM `{pre}settings` WHERE `module_id` = {$seoModuleId};",
                        "UPDATE `{pre}settings` SET `module_id` = {$seoModuleId}, `name` = SUBSTRING(`name`, 5) WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
                    ]);
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
            if ($this->schemaHelper->getContainer()->has('minify.installer.schema') &&
                $this->schemaHelper->getSystemModuleRepository()->moduleExists('minify') === false
            ) {
                $installer = $this->schemaHelper->getContainer()->get('core.modules.schemaInstaller');
                $moduleSchema = $this->schemaHelper->getContainer()->get('minify.installer.schema');
                $result = $installer->install($moduleSchema);
                $aclResult = $this->schemaHelper->getContainer()->get('core.modules.aclInstaller')->install($moduleSchema);
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
}
