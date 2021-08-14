<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration33 extends AbstractMigration
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
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}emoticons` MODIFY COLUMN `code` VARCHAR(10) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}emoticons` MODIFY COLUMN `description` VARCHAR(15) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}emoticons` MODIFY COLUMN `img` VARCHAR(20) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}emoticons` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
    }

    public function down(): void
    {
    }
}
