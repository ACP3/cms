<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration39 implements MigrationInterface
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
        return 39;
    }

    public function up(): void
    {
        $this->db->executeQuery("UPDATE `{$this->db->getPrefix()}menu_items` SET mode = 2 WHERE mode = 4;");
    }

    public function down(): void
    {
    }
}
