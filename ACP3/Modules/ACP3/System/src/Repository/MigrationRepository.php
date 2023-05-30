<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Repository;

use ACP3\Core\Migration\Repository\MigrationRepositoryInterface;
use ACP3\Core\Repository\AbstractRepository;

class MigrationRepository extends AbstractRepository implements MigrationRepositoryInterface
{
    public const TABLE_NAME = 'migration';
    public const PRIMARY_KEY_COLUMN = 'name';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllAlreadyExecutedMigrations(): array
    {
        if ($this->db->fetchColumn("SHOW TABLES LIKE '{$this->getTableName()}'") === false) {
            return [];
        }

        return array_map(static fn ($result) => $result['name'], $this->db->fetchAll("SELECT `name` FROM {$this->getTableName()}"));
    }
}
