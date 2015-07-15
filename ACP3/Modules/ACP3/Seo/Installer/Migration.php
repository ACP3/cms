<?php

namespace ACP3\Modules\ACP3\Seo\Installer;

use ACP3\Core\Modules;

/**
 * Class Migration
 * @package ACP3\Modules\ACP3\Seo\Installer
 */
class Migration implements Modules\Installer\MigrationInterface
{
    /**
     * @inheritdoc
     * 
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            2 => [
                "DELETE FROM `{pre}settings` WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
                "UPDATE `{pre}settings` SET `module_id` = '{moduleId}' WHERE `module_id` = (SELECT `id` FROM `{pre}modules` WHERE `name` = 'system') AND `name` LIKE 'seo_%';"
            ],
            3 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'admin', 'index', 'settings', '', 7);",
            ],
            4 => [
                "UPDATE `{pre}settings` SET `name` = SUBSTRING(`name`, 5) WHERE `module_id` = '{moduleId}' AND `name` LIKE 'seo_%';",
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
