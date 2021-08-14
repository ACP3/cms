<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration14 implements MigrationInterface
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
        return 14;
    }

    public function up(): void
    {
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` MODIFY COLUMN `uri` VARCHAR(191) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` MODIFY COLUMN `alias` VARCHAR(100) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` MODIFY COLUMN `title` VARCHAR(255) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` MODIFY COLUMN `keywords` VARCHAR(255) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` MODIFY COLUMN `description` VARCHAR(255) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}seo` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
    }

    public function down(): void
    {
    }
}
