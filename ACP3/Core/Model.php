<?php

namespace ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * The table prefix
     *
     * @var string
     */
    protected $prefix = DB_PRE;

    /**
     * The name of the default sql table
     */
    const TABLE_NAME = '';

    /**
     * Injects the dependencies
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Build the SQL limit
     *
     * @param $limitStart
     * @param $resultsPerPage
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

    /**
     * Executes the SQL insert statement
     *
     * @param array $params
     * @param string $tableName
     * @return int
     */
    public function insert(array $params, $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $this->db->insert($this->prefix . $tableName, $params);
            $lastId = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $lastId;
        } catch (\Exception $e) {
            $this->db->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

    /**
     * Executes thr SQL delete statement
     *
     * @param int|array $id
     * @param string $field
     * @param string $tableName
     * @return int
     */
    public function delete($id, $field = '', $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $field = empty($field) ? 'id' : $field;
            $bool = $this->db->delete($this->prefix . $tableName, is_array($id) ? $id : array($field => (int)$id));
            $this->db->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

    /**
     * Executes the SQL update statement
     *
     * @param array $params
     * @param int|array $id
     * @param string $tableName
     * @return int
     */
    public function update(array $params, $id, $tableName = '')
    {
        $this->db->beginTransaction();
        try {
            $tableName = !empty($tableName) ? $tableName : static::TABLE_NAME;
            $where = is_array($id) === true ? $id : array('id' => $id);
            $bool = $this->db->update($this->prefix . $tableName, $params, $where);
            $this->db->commit();
            return $bool;
        } catch (\Exception $e) {
            $this->db->rollback();
            Logger::error('database', $e->getMessage());
            return false;
        }
    }

}
