<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model\Repository;

use ACP3\Core;

class ShareRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'share';

    /**
     * @param int $id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExistsById(int $id): bool
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id",
                ['id' => $id]
            ) > 0;
    }

    /**
     * @param string $uri
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByUri(string $uri): array
    {
        return $this->db->fetchAssoc("SELECT * FROM {$this->getTableName()} WHERE `uri` = ?", [$uri]) ?: [];
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->getTableName()}");
    }
}
