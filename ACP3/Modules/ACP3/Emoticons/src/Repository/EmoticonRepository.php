<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Repository;

use ACP3\Core;

class EmoticonRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'emoticons';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $emoticonId): bool
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultsExist(int $emoticonId): bool
    {
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
            [$emoticonId]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneImageById(int $emoticonId): string
    {
        return $this->db->fetchColumn('SELECT img FROM ' . $this->getTableName() . ' WHERE id = ?', [$emoticonId]);
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(): array
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY id DESC');
    }
}
