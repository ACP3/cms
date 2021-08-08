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
     * @return bool
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function newsletterExists(int $newsletterId, ?int $status = null)
    {
        $where = $status !== null ? ' AND status = :status' : '';

        return (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $where,
                ['id' => $newsletterId, 'status' => $status]
            ) > 0;
    }

    /**
     * @return mixed
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneByIdAndStatus(int $newsletterId, int $status)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = :id  AND status = :status;",
            ['id' => $newsletterId, 'status' => $status]
        );
    }

    /**
     * @return int
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(?int $status = null)
    {
        $where = $status !== null ? ' WHERE status = :status' : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['status' => $status]
        );
    }

    /**
     * @return array
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(?int $status = null, ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = $status !== null ? ' WHERE status = :status' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `date` DESC {$limitStmt}",
            ['status' => $status]
        );
    }
}
