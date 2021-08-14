<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration38 implements MigrationInterface
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
        return 38;
    }

    public function up(): void
    {
        $this->db->executeQuery("INSERT INTO `{$this->db->getPrefix()}acl_resources` (`id`, `module_id`, `area`, `controller`, `page`, `params`, `privilege_id`) VALUES('', (select id from `{$this->db->getPrefix()}modules` WHERE name = 'categories'), 'admin', 'index', 'order', '', 4);");
    }

    public function down(): void
    {
    }
}
