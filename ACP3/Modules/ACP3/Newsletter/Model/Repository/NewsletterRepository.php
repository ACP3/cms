<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core;

class NewsletterRepository extends Core\Model\Repository\AbstractRepository
{
    const TABLE_NAME = 'newsletters';

    /**
     * @param int $newsletterId
     * @param string $status
     *
     * @return bool
     */
    public function newsletterExists($newsletterId, $status = '')
    {
        $where = empty($status) === false ? ' AND status = :status' : '';
        return ((int)$this->db->fetchAssoc(
                "SELECT COUNT(*) FROM {$this->getTableName()} WHERE `id` = :id" . $where,
                ['id' => $newsletterId, 'status' => $status]
            ) > 0);
    }

    /**
     * @param int $newsletterId
     * @param int $status
     *
     * @return array
     */
    public function getOneByIdAndStatus($newsletterId, $status)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE id = :id  AND status = :status;",
            ['id' => $newsletterId, 'status' => $status]
        );
    }

    /**
     * @param string $status
     *
     * @return int
     */
    public function countAll($status = '')
    {
        $where = empty($time) === false ? ' WHERE status = :status' : '';
        return (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['status' => $status]
        );
    }

    /**
     * @param string $status
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($status = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($status) === false ? ' WHERE status = :status' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `date` DESC {$limitStmt}",
            ['status' => $status]
        );
    }
}
