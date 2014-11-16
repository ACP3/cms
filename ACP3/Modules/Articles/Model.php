<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\Articles
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'articles';

    /**
     * @param $id
     * @param string $time
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return $this->db->getConnection()->fetchColumn('SELECT COUNT(*) FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0;
    }

    /**
     * @param $id
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->getConnection()->fetchAssoc('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    /**
     * @param string $time
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
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . $where . ' ORDER BY title ASC' . $limitStmt, array('time' => $time));
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->getConnection()->fetchAll('SELECT * FROM ' . $this->db->getPrefix() . static::TABLE_NAME . ' ORDER BY title ASC');
    }

}
