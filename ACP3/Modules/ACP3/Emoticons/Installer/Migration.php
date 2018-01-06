<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Installer;

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
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            32 => [
                'ALTER TABLE `{pre}emoticons` ENGINE = InnoDB',
            ],
            33 => [
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = {moduleId} AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'create';",
                "UPDATE `{pre}acl_resources` SET `page` = 'manage' WHERE `module_id` = {moduleId} AND `area` = 'admin' AND `controller` = 'index' AND `page` = 'edit';",
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
