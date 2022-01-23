<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Repository;

class ShareRatingsRepository extends \ACP3\Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'share_ratings';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function hasAlreadyRated(string $ipAddress, int $shareId): bool
    {
        return $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `ip` = :ip AND `share_id` = :shareId;",
                ['ip' => $ipAddress, 'shareId' => $shareId]
            ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRatingStatistics(int $shareId): array
    {
        return $this->db->fetchAssoc(
            "SELECT `share_id`, COUNT(*) AS total_ratings, AVG(`stars`) AS average_rating FROM {$this->getTableName()} WHERE `share_id` = :shareId GROUP BY `share_id`;",
            ['shareId' => $shareId]
        ) ?: [];
    }
}
