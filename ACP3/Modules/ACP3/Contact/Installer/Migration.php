<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\Modules;

class Migration extends Modules\Installer\AbstractMigration
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            31 => [
                "UPDATE `{pre}acl_resources` SET privilege_id = 7 WHERE `page` = 'acp_list' AND `module_id` = '{moduleId}';",
            ],
            32 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'sidebar', '', 1);",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "contact/", "contact/index/") WHERE `uri` LIKE "contact/%";',
            ],
            34 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri` = "contact/index/index/" WHERE `uri` = "contact/list/";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri` = "contact/index/imprint/" WHERE `uri` = "contact/imprint/";' : '',
            ],
            35 => [
                'UPDATE `{pre}seo` SET `uri` = "contact/index/index/" WHERE `uri` = "contact/index/list/";',
            ],
            36 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'vat_id', '');",
            ],
            37 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'ceo', '');",
            ],
            38 => [
                "UPDATE `{pre}acl_resources` SET `area` = 'widget' WHERE `module_id` = '{moduleId}' AND `area` = 'sidebar';",
            ],
            39 => [
                "UPDATE `{pre}acl_resources` SET `privilege_id` = 3 WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'index';",
                "INSERT INTO `{pre}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ('{moduleId}', 'admin', 'index', 'settings', '', 7);",
            ],
            40 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'mobile_phone', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'picture_credits', '');",
            ],
            41 => [
                'CREATE TABLE `{pre}contacts` (
                    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `date` DATETIME NOT NULL,
                    `mail` VARCHAR(120) NOT NULL,
                    `name` VARCHAR(80) NOT NULL,
                    `message` TEXT NOT NULL,
                    PRIMARY KEY (`id`)
                ) {ENGINE} {CHARSET};',
            ],
            42 => [
                'ALTER TABLE `{pre}contacts` CONVERT TO {charset};',
                'ALTER TABLE `{pre}contacts` MODIFY COLUMN `mail` VARCHAR(120) {charset} NOT NULL;',
                'ALTER TABLE `{pre}contacts` MODIFY COLUMN `message` TEXT {charset} NOT NULL;',
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
}
