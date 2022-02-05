<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;
use ACP3\Modules\ACP3\System\Migration\Migration78;
use Doctrine\DBAL\Exception as DBALException;

class Migration202202051324 implements MigrationInterface
{
    public function __construct(private Connection $db)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function dependencies(): ?array
    {
        return [
            Migration78::class,
        ];
    }

    /**
     * @throws DBALException
     */
    public function up(): void
    {
        $this->db->executeStatement("ALTER TABLE {$this->db->getPrefix()}seo ADD COLUMN structured_data TEXT");
    }

    public function down(): void
    {
    }
}
