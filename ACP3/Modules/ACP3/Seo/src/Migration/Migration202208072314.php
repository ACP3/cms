<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;
use Doctrine\DBAL\Exception as DBALException;

class Migration202208072314 implements MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function dependencies(): ?array
    {
        return null;
    }

    /**
     * @throws DBALException
     */
    public function up(): void
    {
        $this->db->executeStatement("ALTER TABLE {$this->db->getPrefix()}seo ADD COLUMN canonical VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
    }
}
