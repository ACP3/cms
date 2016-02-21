<?php
namespace ACP3\Core\Model;

use ACP3\Core\DB;

/**
 * Class AbstractRepository
 * @package ACP3\Core\Model
 */
abstract class AbstractRepository
{
    /**
     * The name of the sql table
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
        return $this->db->executeTransactionalQuery(function () use ($params, $tableName) {
            $this->db->getConnection()->insert(
                $this->getTableName($tableName),
                $params
            );
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
    public function delete($id, $field = 'id', $tableName = '')
    {
        return $this->db->executeTransactionalQuery(function () use ($id, $field, $tableName) {
            return $this->db->getConnection()->delete(
                $this->getTableName($tableName),
                $this->getIdentifier($id, $field)
            );
        });
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
        return $this->db->executeTransactionalQuery(function () use ($params, $id, $tableName) {
            return $this->db->getConnection()->update(
                $this->getTableName($tableName),
                $params,
                $this->getIdentifier($id)
            );
        });
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function getTableName($tableName = '')
    {
        return $this->db->getPrefixedTableName(!empty($tableName) ? $tableName : static::TABLE_NAME);
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
     * @param int|string $limitStart
     * @param int|string $resultsPerPage
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
