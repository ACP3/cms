<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration20220819233301 implements MigrationInterface
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
        $this->db->executeStatement("ALTER TABLE `{$this->db->getPrefixedTableName('gallery')}` MODIFY `title` VARCHAR(255) NOT NULL;");
        $this->db->executeStatement("ALTER TABLE `{$this->db->getPrefixedTableName('gallery')}` ADD COLUMN `subtitle` VARCHAR(255) NOT NULL AFTER `title`;");
    }

    public function down(): void
    {
    }
}
