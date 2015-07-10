<?php

namespace ACP3\Modules\ACP3\Contact\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Contact\Installer
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
                "UPDATE `{pre}acl_resources` SET privilege_id = 7 WHERE `page` = 'acp_list' AND `module_id` = '{moduleId}';"
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
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'vat_id', '');"
            ],
            37 => [
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'ceo', '');"
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
