<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;
use ACP3\Modules\ACP3\System\Migration\Migration78;

final class Migration38 implements MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function up(): void
    {
        $this->db->executeStatement(
            "CREATE TABLE `{$this->db->getPrefix()}acl_permission` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `role_id` int(10) unsigned NOT NULL,
                `resource_id` int(10) unsigned NOT NULL,
                `permission` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `role_resource` (`role_id`, `resource_id`),
                FOREIGN KEY (`role_id`) REFERENCES `{$this->db->getPrefix()}acl_roles` (`id`) ON DELETE CASCADE,
                FOREIGN KEY (`resource_id`) REFERENCES `{$this->db->getPrefix()}acl_resources` (`id`) ON DELETE CASCADE
            ) ENGINE = InnoDB CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;",
        );
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return [Migration78::class];
    }
}
