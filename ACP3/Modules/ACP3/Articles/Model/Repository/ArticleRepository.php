<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model\Repository;

use ACP3\Core;

class ArticleRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'articles';

    /**
     * @param int    $articleId
     * @param string $time
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $articleId, string $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id{$period}",
            ['id' => $articleId, 'time' => $time, 'active' => 1]
        ) > 0;
    }

    /**
     * @param string $time
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(string $time = '')
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param string   $time
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `title` ASC{$limitStmt}",
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param string   $time
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLatest(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `start` DESC{$limitStmt}",
            ['time' => $time, 'active' => 1]
        );
    }
}
