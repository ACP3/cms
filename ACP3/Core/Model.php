<?php

namespace ACP3\Core;

/**
 * Class Model
 * @package ACP3\Core
 */
class Model
{
    /**
     * The name of the default sql table
     */
    const TABLE_NAME = '';
    /**
     * @var
     */
    protected $db;

    /**
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    /**
     * Executes the SQL insert statement
     *
     * @param array  $params
     * @param string $tableName
     *
     * @return int
     */
    public function insert(array $params, $tableName = '')
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $this->db->getConnection()->insert($this->db->getPrefix() . $tableName, $params);
            $lastId = (int)$this->db->getConnection()->lastInsertId();
            $this->db->getConnection()->commit();
            return $lastId;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

    /**
     * Executes thr SQL delete statement
     *
     * @param int|array $id
     * @param string    $field
     * @param string    $tableName
     *
     * @return int
     */
    public function delete($id, $field = '', $tableName = '')
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $field = empty($field) ? 'id' : $field;
            $bool = $this->db->getConnection()->delete($this->db->getPrefix() . $tableName, is_array($id) ? $id : [$field => (int)$id]);
            $this->db->getConnection()->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

    /**
     * Executes the SQL update statement
     *
     * @param array     $params
     * @param int|array $id
     * @param string    $tableName
     *
     * @return int
     */
    public function update(array $params, $id, $tableName = '')
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $where = is_array($id) === true ? $id : ['id' => $id];
            $bool = $this->db->getConnection()->update($this->db->getPrefix() . $tableName, $params, $where);
            $this->db->getConnection()->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

    /**
     * Build the SQL limit
     *
     * @param $limitStart
     * @param $resultsPerPage
     *
     * @return string
     */
    protected function _buildLimitStmt($limitStart = '', $resultsPerPage = '')
    {
        if ($limitStart !== '' && $resultsPerPage !== '') {
            return ' LIMIT ' . ((int)$limitStart) . ',' . ((int)$resultsPerPage);
        } elseif ($limitStart !== '') {
            return ' LIMIT ' . ((int)$limitStart);
        }

        return '';
    }
}
