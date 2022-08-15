<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Migration;

use ACP3\Core\Database\Connection;

class Migration20220815222001 implements \ACP3\Core\Migration\MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function dependencies(): ?array
    {
        return null;
    }

    public function up(): void
    {
        $this->db->executeStatement("DELETE FROM `{$this->db->getPrefixedTableName('settings')}` WHERE `name` = 'overlay' AND `module_id` = (SELECT `id` FROM `{$this->db->getPrefixedTableName('modules')}` WHERE `name` = 'gallery');");
    }

    public function down(): void
    {
    }
}
