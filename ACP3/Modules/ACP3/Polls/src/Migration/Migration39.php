<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration39 extends AbstractMigration
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function up(): void
    {
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}polls` MODIFY COLUMN `title` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}polls` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}poll_answers` MODIFY COLUMN `text` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}poll_answers` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}poll_votes` MODIFY COLUMN `ip` VARCHAR(40) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}poll_votes` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
    }

    public function down(): void
    {
    }
}
