<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Repository;

use ACP3\Core;

class NewsletterRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'newsletters';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function newsletterExists(int $newsletterId, ?int $status = null): bool
    {
        $where = $status !== null ? ' AND status = :status' : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $where,
            ['id' => $newsletterId, 'status' => $status]
        ) > 0;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByIdAndStatus(int $newsletterId, int $status): array
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = :id  AND status = :status;",
            ['id' => $newsletterId, 'status' => $status]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(?int $status = null): int
    {
        $where = $status !== null ? ' WHERE status = :status' : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['status' => $status]
        );
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(?int $status = null, ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = $status !== null ? ' WHERE status = :status' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `date` DESC {$limitStmt}",
            ['status' => $status]
        );
    }
}
