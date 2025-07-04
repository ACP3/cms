<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration20250704170600 implements MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function dependencies(): ?array
    {
        return null;
    }

    public function up(): void
    {
        $this->db->getConnection()->delete(
            $this->db->getPrefixedTableName('acl_resources'),
            [
                'module_id' => $this->db->fetchColumn("SELECT `id` FROM `{$this->db->getPrefixedTableName('modules')}` WHERE `name` = 'share'"),
                'area' => 'admin',
                'controller' => 'index',
                'page' => 'settings',
            ]
        );
    }

    public function down(): void
    {
    }
}
