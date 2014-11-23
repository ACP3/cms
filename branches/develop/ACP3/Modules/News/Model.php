<?php

namespace ACP3\Modules\News;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\News
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'news';

    /**
     * @return string
     */
    private function _getPeriod()
    {
        return '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
    }

    /**
     * @param $id
     * @param string $time
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';
        return ((int)$this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->db->getPrefix() . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', [$id]);
    }

    /**
     * @param string $time
     * @param string $categoryId
     * @return int
     */
    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time);
        }

        return count($results);
    }

    /**
     * @param $categoryId
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, ['time' => $time, 'categoryId' => $categoryId]);
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE ' . $this->_getPeriod() : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, ['time' => $time]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->getConnection()->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n, ' . $this->db->getPrefix() . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
    }

    /**
     * @param $fields
     * @param $searchTerm
     * @param $sort
     * @param $time
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sort, $time)
    {
        $period = ' AND ' . $this->_getPeriod();
        return $this->db->getConnection()->fetchAll('SELECT id, title, text FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY start ' . $sort . ', end ' . $sort . ', id ' . $sort, ['time' => $time]);
    }

}