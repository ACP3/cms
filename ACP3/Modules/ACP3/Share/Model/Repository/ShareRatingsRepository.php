<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;

class ShareRatingsRepository extends AbstractRepository
{
    const TABLE_NAME = 'share_ratings';

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function hasAlreadyRated(string $ipAddress, int $shareId): bool
    {
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `ip` = :ip AND `share_id` = :shareId;",
            ['ip' => $ipAddress, 'shareId' => $shareId]
            ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRatingStatistics(int $shareId): array
    {
        return $this->db->fetchAssoc(
            "SELECT `share_id`, COUNT(*) AS total_ratings, AVG(`stars`) AS average_rating FROM {$this->getTableName()} WHERE `share_id` = :shareId GROUP BY `share_id`;",
            ['shareId' => $shareId]
        ) ?: [];
    }
}
