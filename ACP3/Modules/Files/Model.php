<?php

namespace ACP3\Modules\Files;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'files';

    /**
     * @param int    $id
     * @param string $time
     *
     * @return bool
     */
    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0 ? true : false;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->prefix . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', array($id));
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getFileById($id)
    {
        return $this->db->fetchColumn('SELECT file FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
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
        $where = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time, 'categoryId' => $categoryId));
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
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT f.*, c.title AS cat FROM ' . $this->prefix . static::TABLE_NAME . ' AS f, ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c WHERE f.category_id = c.id ORDER BY f.start DESC, f.end DESC, f.id DESC');
    }

}