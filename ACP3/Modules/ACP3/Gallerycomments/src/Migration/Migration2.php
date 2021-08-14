<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallerycomments\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\AbstractMigration;

class Migration2 extends AbstractMigration
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
        $this->db->executeQuery("ALTER TABLE `{$this->db->getPrefix()}gallery` MODIFY `comments` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
    }
}
