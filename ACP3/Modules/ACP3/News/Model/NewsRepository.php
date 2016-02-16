<?php

namespace ACP3\Modules\ACP3\News\Model;

use ACP3\Core;

/**
 * Class NewsRepository
 * @package ACP3\Modules\ACP3\News\Model
 */
class NewsRepository extends Core\Model\AbstractRepository
{
    use Core\Model\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'news';

    /**
     * @param int    $id
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        return ((int)$this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE `id` = :id' . $period,
                ['id' => $id, 'time' => $time]
            ) > 0);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc(
            'SELECT n.*, c.title AS category_title FROM ' . $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id) WHERE n.id = ?',
            [$id]
        );
    }

    /**
     * @param string $time
     * @param string $categoryId
     *
     * @return int
     */
    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';

            return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE category_id = :categoryId' . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC',
                ['time' => $time, 'categoryId' => $categoryId]
            );
        }

        $where = empty($time) === false ? ' WHERE ' . $this->getPublicationPeriod() : '';
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC',
            ['time' => $time]
        );
    }

    /**
     * @param int    $categoryId
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND ' . $this->getPublicationPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            "SELECT * FROM {$this->getTableName()} WHERE category_id = :categoryId{$where} ORDER BY `start` DESC, `end` DESC, `id` DESC {$limitStmt}",
            ['time' => $time, 'categoryId' => $categoryId]
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
            'SELECT * FROM ' . $this->getTableName() . $where . ' ORDER BY `start` DESC, `end` DESC, `id` DESC' . $limitStmt,
            ['time' => $time]
        );
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->getTableName() . ' AS n, ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
    }

    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sort
     * @param string $time
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sort, $time)
    {
        $period = ' AND ' . $this->getPublicationPeriod();
        return $this->db->fetchAll(
            'SELECT id, title, `text` FROM ' . $this->getTableName() . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY `start` ' . $sort . ', `end` ' . $sort . ', id ' . $sort,
            ['time' => $time]
        );
    }

    /**
     * @param int    $categoryId
     * @param string $time
     *
     * @return mixed
     */
    public function getLatestByCategoryId($categoryId, $time)
    {
        $period = ' AND ' . $this->getPublicationPeriod();

        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE category_id = :category_id ' . $period . ' ORDER BY `start` DESC LIMIT 1',
            ['category_id' => $categoryId, 'time' => $time]
        );
    }

    /**
     * @param string $time
     *
     * @return mixed
     */
    public function getLatest($time)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . $this->getPublicationPeriod() . ' ORDER BY `start` DESC LIMIT 1',
            ['time' => $time]
        );
    }
}
