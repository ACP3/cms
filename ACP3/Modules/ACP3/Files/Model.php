<?php

namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Files
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'files';

    /**
     * @return string
     */
    protected function _getPeriod()
    {
        return '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
    }

    /**
     * @param int    $id
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->db->getPrefix() . \ACP3\Modules\ACP3\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', [$id]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getFileById($id)
    {
        return $this->db->fetchColumn('SELECT file FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param        $time
     * @param string $categoryId
     *
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
     * @param        $categoryId
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY START DESC, END DESC, id DESC' . $limitStmt, ['time' => $time, 'categoryId' => $categoryId]);
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
        $where = empty($time) === false ? ' WHERE ' . $this->_getPeriod() : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY START DESC, END DESC, id DESC' . $limitStmt, ['time' => $time]);
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT f.*, c.title AS cat FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' AS f, ' . $this->db->getPrefix() . \ACP3\Modules\ACP3\Categories\Model::TABLE_NAME . ' AS c WHERE f.category_id = c.id ORDER BY f.start DESC, f.end DESC, f.id DESC');
    }

    /**
     * @param $fields
     * @param $searchTerm
     * @param $sort
     * @param $time
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sort, $time)
    {
        $period = ' AND ' . $this->_getPeriod();
        return $this->db->fetchAll('SELECT id, title, text FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY START ' . $sort . ', END ' . $sort . ', id ' . $sort, ['time' => $time]);
    }
}
