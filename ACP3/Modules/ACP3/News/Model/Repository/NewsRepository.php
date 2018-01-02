<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model\Repository;

use ACP3\Core;

/**
 * Class NewsRepository
 * @package ACP3\Modules\ACP3\News\Model\Repository
 */
class NewsRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'news';

    /**
     * @param int $newsId
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($newsId, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        return ((int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `id` = :id' . $period,
                ['id' => $newsId, 'time' => $time, 'active' => 1]
            ) > 0);
    }

    /**
     * @param string $time
     * @param int[] $categoryId
     *
     * @return int
     */
    public function countAll($time = '', array $categoryId = [])
    {
        if (!empty($categoryId)) {
            $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

            return (int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . " WHERE `category_id` IN(:categoryId) {$where} ORDER BY `start` DESC, `end` DESC, `id` DESC",
                ['time' => $time, 'categoryId' => $categoryId, 'active' => 1],
                ['time' => \PDO::PARAM_STR, 'categoryId' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, 'active' => \PDO::PARAM_INT]
            );
        }

        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        return (int)$this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC',
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param int[] $categoryIds
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllByCategoryId(array $categoryIds, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE category_id IN(:categoryId) {$where} ORDER BY `start` DESC, `end` DESC, `id` DESC {$limitStmt}",
            ['time' => $time, 'categoryId' => $categoryIds, 'active' => 1],
            ['time' => \PDO::PARAM_STR, 'categoryId' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, 'active' => \PDO::PARAM_INT]
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
        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `start` DESC, `end` DESC, `id` DESC" . $limitStmt,
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param int $categoryId
     * @param string $time
     *
     * @return array
     */
    public function getLatestByCategoryId($categoryId, $time)
    {
        $period = " AND {$this->getPublicationPeriod()} AND `active` = :active";

        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE `category_id` = :category_id {$period} ORDER BY `start` DESC LIMIT 1",
            ['category_id' => $categoryId, 'time' => $time, 'active' => 1]
        );
    }

    /**
     * @param string $time
     *
     * @return array
     */
    public function getLatest($time)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE {$this->getPublicationPeriod()} AND `active` = :active ORDER BY `start` DESC LIMIT 1",
            ['time' => $time, 'active' => 1]
        );
    }
}
