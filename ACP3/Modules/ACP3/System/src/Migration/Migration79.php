<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Migration;

use ACP3\Core\Database\Connection;
use ACP3\Core\Migration\MigrationInterface;

final class Migration79 implements MigrationInterface
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \JsonException
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function up(): void
    {
        $this->db->executeQuery("DELETE FROM `{$this->db->getPrefix()}settings` WHERE `name` = 'page_cache_is_enabled' AND `module_id` = (SELECT `id` FROM `{$this->db->getPrefix()}modules` WHERE `name` = 'system');");
    }

    public function down(): void
    {
    }

    public function dependencies(): ?array
    {
        return [Migration78::class];
    }
}
