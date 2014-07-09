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

    public function resultExists($id, $time = '')
    {
        $period = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id' . $period, array('id' => $id, 'time' => $time)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT n.*, c.title AS category_title FROM ' . $this->prefix . static::TABLE_NAME . ' AS n LEFT JOIN ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c ON(n.category_id = c.id) WHERE n.id = ?', array($id));
    }

    public function countAll($time = '', $categoryId = '')
    {
        if (!empty($categoryId)) {
            $results = $this->getAllByCategoryId($categoryId, $time);
        } else {
            $results = $this->getAll($time);
        }

        return count($results);
    }

    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time));
    }

    public function getAllByCategoryId($categoryId, $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE category_id = :categoryId' . $where . ' ORDER BY start DESC, end DESC, id DESC' . $limitStmt, array('time' => $time, 'categoryId' => $categoryId));
    }

    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->prefix . static::TABLE_NAME . ' AS n, ' . $this->prefix . \ACP3\Modules\Categories\Model::TABLE_NAME . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
    }

}