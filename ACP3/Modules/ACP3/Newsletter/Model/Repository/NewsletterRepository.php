<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core;

class NewsletterRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'newsletters';

    /**
     * @param int      $newsletterId
     * @param int|null $status
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function newsletterExists(int $newsletterId, ?int $status = null)
    {
        $where = empty($status) === false ? ' AND status = :status' : '';

        return (int) $this->db->fetchAssoc(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $where,
                ['id' => $newsletterId, 'status' => $status]
            ) > 0;
    }

    /**
     * @param int $newsletterId
     * @param int $status
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByIdAndStatus(int $newsletterId, int $status)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = :id  AND status = :status;",
            ['id' => $newsletterId, 'status' => $status]
        );
    }

    /**
     * @param int|null $status
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(?int $status = null)
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';

        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['status' => $status]
        );
    }

    /**
     * @param int|null $status
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(?int $status = null, ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = empty($status) === false ? ' WHERE status = :status' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `date` DESC {$limitStmt}",
            ['status' => $status]
        );
    }
}
