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

    public function getRatingsStats(int $shareId): array
    {
        return $this->db->fetchAssoc(
            "SELECT COUNT(*) AS total_ratings, AVG(`stars`) AS average_rating FROM {$this->getTableName()} WHERE `share_id` = :shareId GROUP BY `share_id`;",
            ['shareId' => $shareId]
        ) ?: [];
    }
}
