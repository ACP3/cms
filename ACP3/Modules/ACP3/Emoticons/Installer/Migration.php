<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Installer;

use ACP3\Core\Modules;

class Migration implements Modules\Installer\MigrationInterface
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
                "DELETE FROM `{pre}acl_resources` WHERE `module_id` = '{moduleId}' AND `page` = 'functions';",
            ],
            32 => [
                'ALTER TABLE `{pre}emoticons` ENGINE = InnoDB',
            ],
            33 => [
                "ALTER TABLE `{pre}emoticons` CONVERT TO {charset};",
                "ALTER TABLE `{pre}emoticons` MODIFY COLUMN `code` VARCHAR(10) {charset} NOT NULL;",
                "ALTER TABLE `{pre}emoticons` MODIFY COLUMN `description` VARCHAR(15) {charset} NOT NULL;",
                "ALTER TABLE `{pre}emoticons` MODIFY COLUMN `img` VARCHAR(20) {charset} NOT NULL;",
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
