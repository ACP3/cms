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
     * @var \ACP3\Core\DB
     */
    protected $db;

    /**
     * @param \ACP3\Core\DB $db
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
     * @return int|bool
     */
    public function insert(array $params, $tableName = '')
    {
        return $this->executeTransactionalQuery(function () use ($params, $tableName) {
            $this->db->getConnection()->insert($this->getTableName($tableName), $params);
            return (int)$this->db->getConnection()->lastInsertId();
        });
    }

    /**
     * Executes thr SQL delete statement
     *
     * @param int|array $id
     * @param string    $field
     * @param string    $tableName
     *
     * @return int|bool
     */
    public function delete($id, $field = '', $tableName = '')
    {
        return $this->executeTransactionalQuery(function () use ($id, $field, $tableName) {
            return $this->db->getConnection()->delete($this->getTableName($tableName), $this->getIdentifier($id, $field));
        });
    }

    /**
     * @param callable $callback
     *
     * @return int|bool
     * @throws \Doctrine\DBAL\ConnectionException
     */
    protected function executeTransactionalQuery(callable $callback)
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $result = $callback();
            $this->db->getConnection()->commit();
            return $result;
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
     * @return int|bool
     */
    public function update(array $params, $id, $tableName = '')
    {
        return $this->executeTransactionalQuery(function () use ($params, $id, $tableName) {
            return $this->db->getConnection()->update($this->getTableName($tableName), $params, $this->getIdentifier($id));
        });
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    private function getTableName($tableName)
    {
        return $this->db->getPrefix() . (!empty($tableName) ? $tableName : static::TABLE_NAME);
    }

    /**
     * @param int|array $id
     * @param string    $fieldName
     *
     * @return array
     */
    private function getIdentifier($id, $fieldName = 'id')
    {
        return is_array($id) === true ? $id : [$fieldName => (int)$id];
    }

    /**
     * Build the SQL limit
     *
     * @param $limitStart
     * @param $resultsPerPage
     *
     * @return string
     */
    protected function buildLimitStmt($limitStart = '', $resultsPerPage = '')
    {
        if ($limitStart !== '' && $resultsPerPage !== '') {
            return ' LIMIT ' . ((int)$limitStart) . ',' . ((int)$resultsPerPage);
        } elseif ($limitStart !== '') {
            return ' LIMIT ' . ((int)$limitStart);
        }

        return '';
    }
}
