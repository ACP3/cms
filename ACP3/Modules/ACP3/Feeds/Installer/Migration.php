<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Installer;

class Migration implements \ACP3\Core\Installer\MigrationInterface
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
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', '{moduleId}', 'acp_list', '', 7);",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'feed_image', '');",
                "INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', '{moduleId}', 'feed_type', 'RSS 2.0');",
            ],
            32 => [
                "UPDATE `{pre}acl_resources` SET `privilege_id` = 3 WHERE `module_id` = '{moduleId}' AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'index';",
                "INSERT INTO `{pre}acl_resources` (`module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ('{moduleId}', 'admin', 'index', 'settings', '', 7);",
            ],
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
