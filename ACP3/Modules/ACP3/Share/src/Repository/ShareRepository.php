<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Repository;

use ACP3\Core;

class ShareRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'share';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExistsById(int $id): bool
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id",
                ['id' => $id]
            ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByUri(string $uri): array
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE `uri` = ?", [$uri]) ?: [];
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}");
    }
}
