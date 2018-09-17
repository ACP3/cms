<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Installer;

use ACP3\Core\Modules\Installer\MigrationInterface;

class Migration implements MigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function schemaUpdates()
    {
        return [
            2 => [
                'ALTER TABLE `{pre}auditlog` MODIFY COLUMN `action` VARCHAR(255) {charset} NOT NULL;',
                'ALTER TABLE `{pre}auditlog` CONVERT TO {charset};',
            ],
            3 => [
                'ALTER TABLE `{pre}auditlog` ADD COLUMN `table_name` VARCHAR(255) NOT NULL AFTER `module_id`;',
                'TRUNCATE TABLE `{pre}auditlog`;',
            ],
            4 => [
                "INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES ('', {moduleId}, 'admin', 'index', 'table', '', 3);",
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
