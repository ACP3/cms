<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

final class Migration39 implements MigrationInterface
{
    public function __construct(private Connection $db)
    {
    }

    public function up(): void
    {
        $this->db->executeStatement(
            "insert into `{$this->db->getPrefix()}acl_permission` (`role_id`, `resource_id`, `permission`)
select aru.role_id, are.id, IF(aru.permission = 0, 2, aru.permission)
from `{$this->db->getPrefix()}acl_resources` are
left join `{$this->db->getPrefix()}modules` m on (m.id = are.module_id)
left join `{$this->db->getPrefix()}acl_rules` aru on (aru.module_id = are.module_id and aru.privilege_id = are.privilege_id)
group by are.id, aru.role_id, aru.permission;",
        );
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return [Migration38::class];
    }
}
