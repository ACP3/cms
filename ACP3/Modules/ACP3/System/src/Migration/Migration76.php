<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration76 implements MigrationInterface
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getSchemaVersion(): int
    {
        return 76;
    }

    public function up(): void
    {
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}modules` DROP COLUMN `active`;");
    }

    public function down(): void
    {
    }
}
