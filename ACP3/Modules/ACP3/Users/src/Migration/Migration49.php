<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration49 extends AbstractMigration
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
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `nickname` VARCHAR(30) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `pwd` VARCHAR(128) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `pwd_salt` VARCHAR(16) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `remember_me_token` VARCHAR(128) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `realname` VARCHAR(80) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `birthday` VARCHAR(10) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `mail` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `website` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `icq` VARCHAR(11) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `skype` VARCHAR(30) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `street` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `house_number` VARCHAR(5) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `zip` VARCHAR(6) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `city` VARCHAR(120) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` MODIFY COLUMN `country` CHAR(2) CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci` NOT NULL;");
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}users` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_unicode_ci`;");
    }

    public function down(): void
    {
    }
}
