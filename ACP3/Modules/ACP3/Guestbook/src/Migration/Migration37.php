<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration37 implements MigrationInterface
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
        return 37;
    }

    public function up(): void
    {
        $this->db->executeQuery("DELETE FROM `{$this->db->getPrefix()}settings` WHERE `module_id` = (select id from `{$this->db->getPrefix()}modules` WHERE name = 'guestbook') AND `name` = 'newsletter_integration';");
    }

    public function down(): void
    {
    }
}
