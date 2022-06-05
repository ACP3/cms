<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

final class Migration40 implements MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function up(): void
    {
        $this->db->executeStatement("ALTER TABLE {$this->db->getPrefix()}acl_resources DROP FOREIGN KEY {$this->db->getPrefix()}acl_resources_ibfk_1;");
        $this->db->executeStatement("ALTER TABLE {$this->db->getPrefix()}acl_resources DROP COLUMN privilege_id;");
        $this->db->executeStatement("DROP TABLE {$this->db->getPrefix()}acl_rules;");
        $this->db->executeStatement("DROP TABLE {$this->db->getPrefix()}acl_privileges;");
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return [Migration39::class];
    }
}
