<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Model\Repository;

use ACP3\Core;

class NewsRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'news';

    /**
     * @param int    $newsId
     * @param string $time
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $newsId, string $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return (int) $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `id` = :id' . $period,
                ['id' => $newsId, 'time' => $time, 'active' => 1]
            ) > 0;
    }

    /**
     * @param int $newsId
     *
     * @return array|mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById($newsId)
    {
        return $this->db->fetchAssoc(
            'SELECT n.*, c.title AS category_title FROM ' . $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id) WHERE n.id = ?',
            [$newsId]
        );
    }

    /**
     * @param string   $time
     * @param int|null $categoryId
     *
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(string $time = '', ?int $categoryId = null)
    {
        if (!empty($categoryId)) {
            return $this->countAllByCategoryId([$categoryId], $time);
        }

        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . $where,
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param array  $categoryId
     * @param string $time
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAllByCategoryId(array $categoryId, string $time = ''): int
    {
        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';

        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . " WHERE `category_id` IN(:categoryId) {$where}",
            ['time' => $time, 'categoryId' => $categoryId, 'active' => 1],
            0,
            ['time' => \PDO::PARAM_STR, 'categoryId' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, 'active' => \PDO::PARAM_INT]
        );
    }

    /**
     * @param int[]|int $categoryId
     * @param string    $time
     * @param int|null  $limitStart
     * @param int|null  $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllByCategoryId(
        $categoryId,
        string $time = '',
        ?int $limitStart = null,
        ?int $resultsPerPage = null
    ) {
        if (false === \is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() . ' AND `active` = :active' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE category_id IN(:categoryId) {$where} ORDER BY `start` DESC, `end` DESC, `id` DESC {$limitStmt}",
            ['time' => $time, 'categoryId' => $categoryId, 'active' => 1],
            ['time' => \PDO::PARAM_STR, 'categoryId' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY, 'active' => \PDO::PARAM_INT]
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
            "SELECT * FROM {$this->getTableName()}{$where} ORDER BY `start` DESC, `end` DESC, `id` DESC" . $limitStmt,
            ['time' => $time, 'active' => 1]
        );
    }

    /**
     * @param int    $categoryId
     * @param string $time
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLatestByCategoryId(int $categoryId, string $time)
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
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLatest(string $time)
    {
        return $this->db->fetchAssoc(
            "SELECT * FROM {$this->getTableName()} WHERE {$this->getPublicationPeriod()} AND `active` = :active ORDER BY `start` DESC LIMIT 1",
            ['time' => $time, 'active' => 1]
        );
    }
}
