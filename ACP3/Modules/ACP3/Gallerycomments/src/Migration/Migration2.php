<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

class Migration2 implements MigrationInterface
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
        return 2;
    }

    public function up(): void
    {
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}gallery` MODIFY `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
    }
}
