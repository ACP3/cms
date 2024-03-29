<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;
use Doctrine\DBAL\Exception;

final class Migration79 implements MigrationInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * @throws Exception
     */
    public function up(): void
    {
        $this->db->executeStatement("DELETE FROM `{$this->db->getPrefixedTableName('settings')}` WHERE `name` = 'page_cache_is_enabled' AND `module_id` = (SELECT `id` FROM `{$this->db->getPrefixedTableName('modules')}` WHERE `name` = 'system');");
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return [Migration78::class];
    }
}
