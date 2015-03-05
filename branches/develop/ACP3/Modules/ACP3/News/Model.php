<?php

namespace ACP3\Modules\ACP3\News;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\News
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'news';

    /**
     * @return string
     */
    protected function _getPeriod()
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
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0);
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->db->getPrefix() . \ACP3\Modules\ACP3\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', [$id]);
    }

    /**
     * @param string $time
     * @param string $categoryId
     * @return int
     */
    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $where = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';

            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC', ['time' => $time, 'categoryId' => $categoryId]);
        } else {
            $where = empty($time) === false ? ' WHERE ' . $this->_getPeriod() : '';
            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC', ['time' => $time]);
        }
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
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, ['time' => $time, 'categoryId' => $categoryId]);
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
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, ['time' => $time]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n, ' . $this->db->getPrefix() . \ACP3\Modules\ACP3\Categories\Model::TABLE_NAME . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
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
        return $this->db->fetchAll('SELECT id, title, text FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY start ' . $sort . ', end ' . $sort . ', id ' . $sort, ['time' => $time]);
    }

    /**
     * @param $categoryId
     * @param $time
     *
     * @return mixed
     */
    public function getLatestByCategoryId($categoryId, $time)
    {
        $period = ' AND ' . $this->_getPeriod();

        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE category_id = :category_id ' . $period . ' ORDER BY start DESC LIMIT 1', ['category_id' => $categoryId, 'time' => $time]);
    }

    /**
     * @param $time
     *
     * @return mixed
     */
    public function getLatest($time)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE ' . $this->_getPeriod() . ' ORDER BY start DESC LIMIT 1', ['time' => $time]);
    }
}
