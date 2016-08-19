<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model\Repository;

use ACP3\Core;

/**
 * Class ArticleRepository
 * @package ACP3\Modules\ACP3\Articles\Model\Repository
 */
class ArticleRepository extends Core\Model\AbstractRepository
{
    use Core\Model\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'articles';

    /**
     * @param int    $articleId
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($articleId, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} WHERE id = :id{$period}",
            ['id' => $articleId, 'time' => $time]) > 0;
    }

    /**
     * @param string $time
     *
     * @return int
     */
    public function countAll($time = '')
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() : '';
        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['time' => $time]
        );
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `title` ASC{$limitStmt}",
            ['time' => $time]
        );
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getLatest($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `start` DESC{$limitStmt}",
            ['time' => $time]
        );
    }

    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sortDirection
     * @param string $time
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sortDirection, $time)
    {
        return $this->db->fetchAll(
            "SELECT `id`, `title`, `text` FROM {$this->getTableName()} WHERE MATCH ({$fields}) AGAINST ({$this->db->getConnection()->quote($searchTerm)} IN BOOLEAN MODE) AND {$this->getPublicationPeriod()} ORDER BY `start` {$sortDirection}, `end` {$sortDirection}, `title` {$sortDirection}",
            ['time' => $time]
        );
    }
}
