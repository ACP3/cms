<?php

namespace ACP3\Modules\ACP3\Gallery\Installer;

use ACP3\Core\Modules;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\Permissions;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Gallery\Installer
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
                "ALTER TABLE `{pre}gallery` CHANGE `name` `title` VARCHAR(120) {CHARSET} NOT NULL;",
            ],
            32 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            33 => [
                'UPDATE `{pre}seo` SET `uri`=REPLACE(`uri`, "gallery/", "gallery/index/") WHERE `uri` LIKE "gallery/%";',
            ],
            34 => [
                "UPDATE `{pre}acl_resources` SET `controller` = 'pictures' WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_picture';",
                "UPDATE `{pre}acl_resources` SET `page` = REPLACE(`page`, '_picture', '') WHERE `module_id` = '{moduleId}' AND `page` LIKE '%_picture';",
                "UPDATE `{pre}acl_resources` SET `controller` = 'pictures' WHERE `module_id` = '{moduleId}' AND `page` = 'order';",
            ],
            35 => [
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/list/", "gallery/index/index/") WHERE `uri` LIKE "gallery/list/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/pics/", "gallery/index/pics/") WHERE `uri` LIKE "gallery/pics/%";' : '',
                $this->schemaHelper->moduleIsInstalled('menus') || $this->schemaHelper->moduleIsInstalled('menu_items') ? 'UPDATE `{pre}menu_items` SET `uri`=REPLACE(`uri`, "gallery/details/", "gallery/index/details/") WHERE `uri` LIKE "gallery/details/%";' : '',
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
}
