<?php
namespace ACP3\Modules\ACP3\Articles;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Articles
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'articles';

    /**
     * @return string
     */
    protected function _getPeriod()
    {
        return '(start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
    }

    /**
     * @param        $id
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND ' . $this->_getPeriod() : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, ['id' => $id, 'time' => $time]) > 0;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', [$id]);
    }

    /**
     * @param string $time
     *
     * @return int
     */
    public function countAll($time = '')
    {
        return count($this->getAll($time));
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
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY title ASC' . $limitStmt, ['time' => $time]);
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
        $where = empty($time) === false ? ' WHERE ' . $this->_getPeriod() : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY `start` DESC' . $limitStmt, ['time' => $time]);
    }


    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY title ASC');
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
        return $this->db->fetchAll('SELECT id, title, text FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE MATCH (' . $fields . ') AGAINST (' . $this->db->getConnection()->quote($searchTerm) . ' IN BOOLEAN MODE)' . $period . ' ORDER BY START ' . $sort . ', END ' . $sort . ', title ' . $sort, ['time' => $time]);
    }
}
