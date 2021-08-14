<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration3 implements MigrationInterface
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
        return 3;
    }

    public function up(): void
    {
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}share` MODIFY COLUMN `uri` VARCHAR(191) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}share` MODIFY COLUMN `services` TEXT CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}share` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}share_ratings` MODIFY COLUMN `ip` VARCHAR(40) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}share_ratings` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
    }

    public function down(): void
    {
    }
}
